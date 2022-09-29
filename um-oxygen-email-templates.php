<?php
/**
 * Plugin Name:     Ultimate Member - Oxygen Builder Email Templates
 * Description:     Extension to Ultimate Member for integration of UM email templates with Oxygen Builder.
 * Version:         1.0.0
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.5.0
 */


if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'UM' ) ) return;

define( 'THEME_OXYGEN', '/theme-oxygen/ultimate-member/email/' );


remove_filter( 'um_change_settings_before_save', array( UM()->admin_settings(), 'save_email_templates' ) );

add_filter( 'um_change_settings_before_save', 'save_email_templates_oxygen', 10, 1 );

add_filter( 'um_get_template',          'um_get_template_oxygen', 10, 4 );
add_filter( 'um_locate_template',       'um_locate_template_oxygen', 10, 3 );
add_filter( 'um_locate_email_template', 'um_locate_email_template_oxygen', 10, 2 );

function um_get_template_oxygen( $located, $template_name, $path, $t_args ) {

    $template_oxygen = WP_CONTENT_DIR . THEME_OXYGEN . $template_name . ".php";
    if( file_exists( $template_oxygen )) return $template_oxygen;
    return $located;
 }

function um_locate_template_oxygen( $template, $template_name, $path ) {

    $template_oxygen = WP_CONTENT_DIR . THEME_OXYGEN . $template_name . ".php";
    if( file_exists( $template_oxygen )) return $template_oxygen;
    return $template;
}

function um_locate_email_template_oxygen( $template, $template_name ) {

    $template_oxygen = WP_CONTENT_DIR . THEME_OXYGEN . $template_name . ".php";
    if( file_exists( $template_oxygen )) return $template_oxygen;
    return $template;
}

function save_email_templates_oxygen( $settings ) {

    if ( empty( $settings['um_email_template'] ) ) {
        return $settings;
    }

    $template = $settings['um_email_template'];
    $content = wp_kses_post( stripslashes( $settings[ $template ] ) );

    $theme_template_path = trailingslashit( WP_CONTENT_DIR . THEME_OXYGEN ) . $template . '.php';
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

        if ( file_exists( $plugin_template_path ) && is_writable( WP_CONTENT_DIR . THEME_OXYGEN )) {
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

