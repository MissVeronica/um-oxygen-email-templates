<?php
/**
 * Plugin Name:     Ultimate Member - Oxygen Builder Custom Templates
 * Description:     Extension to Ultimate Member for integration of custom UM email and profile templates with Oxygen Builder or any other User selectable folder path.
 * Version:         2.3.0
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.8.3
 */


if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'UM' ) ) return;

class UM_Oxygen_Builder_Custom_Templates {

    function __construct( ) {

        $email_templates_oxygen_folder = trailingslashit( trim( sanitize_text_field( UM()->options()->get( 'email_templates_oxygen_folder' ))));
        if ( empty( $email_templates_oxygen_folder )) {
            $email_templates_oxygen_folder = '/theme-oxygen/ultimate-member/email/';
        }

        $profile_templates_oxygen_folder = trailingslashit( trim( sanitize_text_field( UM()->options()->get( 'profile_templates_oxygen_folder' ))));
        if ( empty( $profile_templates_oxygen_folder )) {
            $profile_templates_oxygen_folder = '/theme-oxygen/ultimate-member/templates/';
        }

        define( 'UM_EMAIL_OXYGEN', $email_templates_oxygen_folder );
        define( 'UM_PROFILE_OXYGEN', $profile_templates_oxygen_folder );

        remove_filter( 'um_change_settings_before_save', array( UM()->admin_settings(), 'save_email_templates' ) );

        add_filter( 'um_change_settings_before_save', array( $this, 'save_email_templates_oxygen' ), 10, 1 );
        add_filter( 'um_settings_structure',          array( $this, 'um_settings_structure_templates_oxygen' ), 10, 1 );
        add_filter( 'um_get_template',                array( $this, 'um_get_template_oxygen' ), 10, 4 );
        add_filter( 'um_locate_template',             array( $this, 'um_locate_template_oxygen' ), 10, 3 );
        add_filter( 'um_locate_email_template',       array( $this, 'um_locate_email_template_oxygen' ), 10, 2 );
    }

    public function um_get_template_oxygen( $located, $template_name, $path, $t_args ) {

        $template_oxygen = WP_CONTENT_DIR . UM_EMAIL_OXYGEN . $template_name . ".php";
        if( file_exists( $template_oxygen )) return $template_oxygen;
        return $located;
    }

    public function um_locate_template_oxygen( $template, $template_name, $path ) {

        $template_oxygen = WP_CONTENT_DIR . UM_EMAIL_OXYGEN . $template_name . ".php";
        if( file_exists( $template_oxygen )) return $template_oxygen;

        $template_oxygen = WP_CONTENT_DIR . UM_PROFILE_OXYGEN . $template_name;
        if( file_exists( $template_oxygen )) return $template_oxygen;

        return $template;
    }

    public function um_locate_email_template_oxygen( $template, $template_name ) {

        $template_oxygen = WP_CONTENT_DIR . UM_EMAIL_OXYGEN . $template_name . ".php";
        if( file_exists( $template_oxygen )) return $template_oxygen;
        return $template;
    }

    public function save_email_templates_oxygen( $settings ) {

        if ( empty( $settings['um_email_template'] ) ) {
            return $settings;
        }

        $template = $settings['um_email_template'];
        $content = wp_kses_post( stripslashes( $settings[ $template ] ) );

        $theme_template_path = trailingslashit( WP_CONTENT_DIR . UM_EMAIL_OXYGEN ) . $template . '.php';
        if ( ! file_exists( $theme_template_path ) ) {       

            $plugin_template_path = trailingslashit( um_path . 'templates/email/' ) . $template . '.php';
            $temp_path = str_replace( WP_CONTENT_DIR .'/', '', $theme_template_path );
            $temp_path = str_replace( '/', DIRECTORY_SEPARATOR, $temp_path );

            $folders = explode( DIRECTORY_SEPARATOR, $temp_path );
            $folders = array_splice( $folders, 0, count( $folders ) - 1 );
            $cur_folder = '';
            $theme_dir = trailingslashit( WP_CONTENT_DIR );

            foreach ( $folders as $folder ) {
                $prev_dir = $cur_folder;
                $cur_folder .= $folder . DIRECTORY_SEPARATOR;
                if ( ! is_dir( $theme_dir . $cur_folder ) && wp_is_writable( $theme_dir . $prev_dir ) ) {
                    mkdir( $theme_dir . $cur_folder, 0777 );
                }
            }

            if ( file_exists( $plugin_template_path ) && is_writable( WP_CONTENT_DIR . UM_EMAIL_OXYGEN )) {
                copy( $plugin_template_path, $theme_template_path );
            }
        }

        if ( is_writable( $theme_template_path )) {
            $fp = fopen( $theme_template_path, "w" );
            if( $fp !== false ) {
                $result = fputs( $fp, $content );
                fclose( $fp );

                if ( $result !== false ) {
                    unset( $settings['um_email_template'] );
                    unset( $settings[ $template ] );
                }
            }
        }
        return $settings;
    }

    public function um_settings_structure_templates_oxygen( $settings_structure ) {

        $settings_structure['email']['form_sections']['oxygen']['title']       = __( 'Oxygen Builder Custom Templates', 'ultimate-member' );
        $settings_structure['email']['form_sections']['oxygen']['description'] = __( 'Plugin version 2.3.0 - tested with UM 2.8.3', 'ultimate-member' );

        $settings_structure['email']['form_sections']['oxygen']['fields'][] =
 
                array(
                    'id'          => 'email_templates_oxygen_folder',
                    'type'        => 'text',
                    'label'       => __( 'Email Templates - Customized Email Folder Path', 'ultimate-member' ),
                    'description' => __( 'Enter the folder path where you will save your customized email templates. Example: /theme-oxygen/ultimate-member/email/ ', 'ultimate-member' ),
                    'size'        => 'medium',
                    'default'     => '/theme-oxygen/ultimate-member/email/',
                );

        $settings_structure['email']['form_sections']['oxygen']['fields'][] =
 
                array(
                    'id'          => 'profile_templates_oxygen_folder',
                    'type'        => 'text',
                    'label'       => __( 'Profile Templates - Customized Profile Folder Path', 'ultimate-member' ),
                    'description' => __( 'Enter the folder path where you will save your customized profile templates. Example: /theme-oxygen/ultimate-member/templates/ ', 'ultimate-member' ),
                    'size'        => 'medium',
                    'default'     => '/theme-oxygen/ultimate-member/templates/',
                );

        return $settings_structure;
    }
}

new UM_Oxygen_Builder_Custom_Templates();
