<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Sales Workflow
Description: A comprehensive module to manage the sales workflow.
Author: Platinum Team BLabs
Author URI: https://businesslabs.org
Version: 1.0.0
Requires at least: 2.3.*
*/

define('SALES_WORKFLOW_MODULE_NAME', 'sales_workflow');

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(SALES_WORKFLOW_MODULE_NAME, [SALES_WORKFLOW_MODULE_NAME]);

hooks()->add_action('admin_init', 'sales_workflow_init_menu_items');
// hooks()->add_action('admin_init', 'sales_workflow_register_hooks');

function sales_workflow_init_menu_items() {
    $CI = &get_instance();
    $CI->app_menu->add_sidebar_menu_item('sales-workflow-menu', [
        'name'     => 'Sales Workflow',
        'href'     => admin_url('sales_workflow'),
        'position' => 57,
        'icon'     => 'fa fa-tasks',
    ]);
}

/**
* Register activation module hook
*/
register_activation_hook(SALES_WORKFLOW_MODULE_NAME, 'sales_workflow_activation_hook');

function sales_workflow_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

