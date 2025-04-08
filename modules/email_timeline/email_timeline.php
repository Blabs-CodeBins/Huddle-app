<?php

defined('BASEPATH') or exit('No direct script access allowed');
/*
Module Name: Email Timeline
Description: This module handles email conversations and threads for employees and accounts.
Author: Platinum Team BLabs
Author URI: https://businesslabs.org
Version: 1.0.0
Requires at least: 2.3.*
*/
define('EMAIL_TIMELINE_MODULE_NAME', 'email_timeline');

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(EMAIL_TIMELINE_MODULE_NAME, [EMAIL_TIMELINE_MODULE_NAME]);

hooks()->add_action('admin_init', 'email_timeline_init_menu_items');

/**
 * Init email timeline module menu items in setup in admin_init hook
 * @return null
 */
function email_timeline_init_menu_items()
{
    $CI = &get_instance();
    $CI->app_menu->add_sidebar_menu_item('email-timeline-options', [
        'name'     => 'Email Timeline', 
        'collapse' => true,
        'position' => 58, 
        'icon'     => 'fa fa-envelope',
    ]);

    if (is_admin()) {
        $CI->app_menu->add_sidebar_children_item('email-timeline-options', [
            'slug'     => 'email-timeline-menu-options', 
            'name'     => _l('email_timeline'), 
            'href'     => admin_url('email_timeline'), 
            'position' => 77, 
        ]);
        $CI->app_menu->add_sidebar_children_item('email-timeline-options', [
            'slug'     => 'send-email-menu-options',
            'name'     => _l('send_email'), 
            'href'     => admin_url('email_timeline/send_email'), 
            'position' => 78, 
        ]);
        $CI->app_menu->add_sidebar_children_item('email-timeline-options', [
            'slug'     => 'retrieve-email-menu-options', 
            'name'     => _l('retrieve_email'), 
            'href'     => admin_url('email_timeline/retrieve_emails'), 
            'position' => 79,
        ]);
    }
}

/**
* Register activation module hook
*/
register_activation_hook(EMAIL_TIMELINE_MODULE_NAME, 'email_timeline_activation_hook');

function email_timeline_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}
