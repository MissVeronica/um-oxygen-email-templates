# UM Oxygen Builder Custom Email and Profile Templates
Extension to Ultimate Member for integration of custom UM email and profile templates with Oxygen Builder or any other User selectable folder path.

## Solution
Oxygen Builder is not a WP Theme therefor can't UM template structure be used and this plugin is using a fake "Theme" for saving UM email templates.

Customized email folders are saved outside the WP Themes folder and the UM template structure is created by the plugin when required by UM.

Customized Profile templates are loaded when found in the Oxygen "Theme" folder. The folder structure in <code>.../ultimate-member/templates/</code> is maintained like <code>.../ultimate-member/templates/profile/</code>.

## Settings
1. UM Settings -> Email -> "Email Templates - Customized Email Folder"
2. The "Customized Email Folder" is created by the plugin if required.
3. Default folder is "/theme-oxygen/ultimate-member/email/"
4. UM Settings -> Email -> "Profile Templates - Customized Profile Folder"
5. Default folder is "/theme-oxygen/ultimate-member/templates/"

## Installation
1. Download zip file. 
2. Upload the zip file as a new plugin to Wordpress
3. Activate the plugin

## Updates
1. Version 2.0.0 New option: UM Settings -> Email -> "Email Templates - Customized Email Folder"
2. Version 2.1.0 Code improvements
3. Version 2.2.0 New option: UM Settings -> Email -> "Profile Templates - Customized Profile Folder"
