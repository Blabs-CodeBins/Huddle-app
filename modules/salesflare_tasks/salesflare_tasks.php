<?php
/**
 * Ensures that the module init file can't be accessed directly, only within the application.
 */

use function Clue\StreamFilter\fun;

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Salesflare Tasks
Description: This module creates tasks on sequence under salesflare automatically.
Author: Platinum Team BLabs Member - Nasir HYD-006.
Author URI: https://businesslabs.org
Version: 1.0.0
Requires at least: 3.0.4*
*/ 




hooks()->add_action('admin_init', 'salesflare_init_menu_items');

function salesflare_init_menu_items()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('salesflare_menu', [
        'name'     => 'Salesflare Tasks', // The name if the item
        'collapse' => true, // Indicates that this item will have submitems
        'position' => 10, // The menu position
        'icon'     => 'fa-solid fa-list-check', // Font awesome icon
    ]);

    // The first paremeter is the parent menu ID/Slug
    $CI->app_menu->add_sidebar_children_item('salesflare_menu', [
        'slug'     => 'tasks', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Tasks', // The name if the item
        'href'     => admin_url("salesflare/tasks"), // URL of the item
        'position' => 5, // The menu position
        'icon'     => 'fa-solid fa-check', // Font awesome icon
    ]);

    $CI->app_menu->add_sidebar_children_item('salesflare_menu', [
        'slug'     => 'manage', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Manage', // The name if the item
        'href'     => admin_url("salesflare/manage"), // URL of the item
        'position' => 5, // The menu position
        'icon'     => 'fa-solid fa-list', // Font awesome icon
    ]);

    $CI->app_menu->add_sidebar_children_item('salesflare_menu', [
        'slug'     => 'settings', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Settings', // The name if the item
        'href'     => admin_url("salesflare/settings"), // URL of the item
        'position' => 5, // The menu position
        'icon'     => 'fa-solid fa-gear', // Font awesome icon
    ]);
}

