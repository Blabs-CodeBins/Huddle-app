<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: ToDo
Description: This module handles Huddle ToDo of your employees.
Author: Platinum Team BLabs Member - Vikash HYD-23.
Author URI: https://businesslabs.org
Version: 1.0.0
Requires at least: 2.3.*
*/
define('TODO_MODULE_NAME', 'todo');

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(TODO_MODULE_NAME, [TODO_MODULE_NAME]);
hooks()->add_filter('migration_tables_to_replace_old_links', 'todo_migration_tables_to_replace_old_links');
hooks()->add_action('admin_init', 'todo_module_init_menu_items');
hooks()->add_action('admin_init', 'PlanSubmissionCheck');

  
function PlanSubmissionCheck() {
    
    $CI = &get_instance();
    $CI->load->model('staff_model');
    $CI->load->library('session');
    
    $userId = $CI->session->userdata('staff_user_id');
    $staff  = $CI->staff_model->get($userId);
    
    $lastLogin = $staff->last_login ?? null; 
    $istoplinemanager = ($staff->toplinemanager == 1);

    // Get the first login timestamp of today from activity log
    $CI->db->select('MIN(date) AS first_login_time');
    $CI->db->from('tblactivity_log');
    $CI->db->where('staffid', $staff->full_name);
    $CI->db->where('description LIKE', 'User Successfully Logged In%');
    $CI->db->where('DATE(date) = CURDATE()', null, false);
    $query = $CI->db->get();
    $firstLoginTime = $query->row()->first_login_time;

    // Check if 12 hours have passed since first login
    $hoursSinceFirstLogin = (time() - strtotime($firstLoginTime)) / 3600;
    $isTimeSlotOver = ($hoursSinceFirstLogin >= 12);

    // Check if SOD was already submitted today
    $CI->db->where('staffid', $staff->full_name);
    $CI->db->like('description', 'User Successfully Submitted SOD', 'after');
    $CI->db->where('DATE(date) = CURDATE()', null, false);
    $query = $CI->db->get('tblactivity_log'); 
    $sodSubmittedToday = ($query->num_rows() > 0);

    // Don't check for plan submission on plan pages
    $current_controller = $CI->router->fetch_class();
    $current_method = $CI->router->fetch_method();
    if ($current_method === 'create_todays_plan' || $current_method === 'GetTaskbyIds' || ($current_controller === 'todo' && $current_method === 'huddlersod')) {
        return;
    }

    if (!is_admin($userId)) {
        if (!$sodSubmittedToday) {
            $redurectUrl = admin_url().'todo/huddlersod';
            redirect($redurectUrl);
            exit;
        }
            
    }else{
        
        return;
    }  
}



/**
 * Init todo module menu items in setup in admin_init hook
 * @return null
 */
function todo_module_init_menu_items()
{
    $CI = &get_instance();
    $userId = $CI->session->userdata('staff_user_id');
    $staff  = $CI->staff_model->get($userId);
    $istoplinemanager = ($staff->toplinemanager == 1);
    $isProjectManager = ($staff->todolist_pm == 1);
    // Adding main menu item
    $CI->app_menu->add_sidebar_menu_item('todo-options', [
        'name'     => 'Todo',
        'collapse' => true,
        'position' => 2, //81
        'icon'     => 'fa fa-address-card',
    ]);
    // Add main menu items
    if($isProjectManager || $istoplinemanager || is_admin($userId)){
        $CI->app_menu->add_sidebar_children_item('todo-options', [            
            'slug' => 'MyTeamToDos',
            'name' => 'My Team To Dos',
            'href' => admin_url('todo') . '/myteamtodos', // Ensure this URL maps to the correct route
            'position' => 100,
        ]);
    }
    $CI->app_menu->add_sidebar_children_item('todo-options', [            
        'slug' => 'MyToDos',
        'name' => 'My To Dos',
        'href' => admin_url('todo') . '/mytodos', // Ensure this URL maps to the correct route
        'position' => 101,
    ]);
    $CI->app_menu->add_sidebar_children_item('todo-options', [            
        'slug' => 'statistics',
        'name' => 'Statistics',
        'href' => admin_url('todo') . '/getReports', // Ensure this URL maps to the correct route
        'position' => 102,
        // 'parent_slug' => 'settings', // Specify that this is a child of "Settings"
    ]);
   
    if (is_admin($userId)) {
        // Add submenus under "Settings"
        $CI->app_menu->add_sidebar_children_item('todo-options', [            
            'slug' => 'settings',
            'name' => _l('Settings'),
            'href' => admin_url('todo') . '/settings', // Ensure this URL maps to the correct route
            'position' => 103,
            // 'parent_slug' => 'settings', // Specify that this is a child of "Settings"
        ]);
    }
    
}

function todo_migration_tables_to_replace_old_links($tables)
{
    $tables[] = [
        'table' => db_prefix() . 'todo',
        'field' => 'description',
    ];

    return $tables;
}

register_activation_hook(TODO_MODULE_NAME, 'todo_module_activation_hook');

function todo_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

register_deactivation_hook(TODO_MODULE_NAME, 'todo_module_deactivation_hook');

function todo_module_deactivation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/uninstall.php');
}

