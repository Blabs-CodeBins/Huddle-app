<?php

defined('BASEPATH') or exit('No direct script access allowed');
$messages = [];
$ci =& get_instance(); // Get the CodeIgniter instance

// Check if the database prefix function is available and return the correct prefix
if (!function_exists('db_prefix')) {
    die('Error: db_prefix() function is not defined.');
}

$prefix = db_prefix();

// Check if the 'staff' table exists
if ($ci->db->table_exists($prefix . 'staff')) {
    // Remove the 'reports_to' column if it exists
    $column_exists_query = "SHOW COLUMNS FROM " . $prefix . 'staff LIKE \'reports_to\'';
    $column_exists_result = $ci->db->query($column_exists_query);
    
    if ($column_exists_result->num_rows() > 0) {
        // SQL Query to drop the 'reports_to' column
        $alterTableSQL = 'ALTER TABLE ' . $prefix . 'staff DROP COLUMN reports_to;';
        if (!$ci->db->query($alterTableSQL)) {
            die('Error altering table: ' . $ci->db->error()['message']);
        }
        $messages[] = 'Column "reports_to" dropped successfully.';
    } else {
        $messages[] = 'Column "reports_to" does not exist.';
    }

    // Remove the 'todolist_pm' column if it exists
    $column_exists_query = "SHOW COLUMNS FROM " . $prefix . 'staff LIKE \'todolist_pm\'';
    $column_exists_result = $ci->db->query($column_exists_query);
    
    if ($column_exists_result->num_rows() > 0) {
        $alterTableSQL = 'ALTER TABLE ' . $prefix . 'staff DROP COLUMN todolist_pm;';
        if (!$ci->db->query($alterTableSQL)) {
            die('Error altering table: ' . $ci->db->error()['message']);
        }
        $messages[] = 'Column "todolist_pm" dropped successfully.';
    } else {
        $messages[] = 'Column "todolist_pm" does not exist.';
    }

    // Remove the 'toplinemanager' column if it exists
    $column_exists_query = "SHOW COLUMNS FROM " . $prefix . 'staff LIKE \'toplinemanager\'';
    $column_exists_result = $ci->db->query($column_exists_query);
    
    if ($column_exists_result->num_rows() > 0) {
        $alterTableSQL = 'ALTER TABLE ' . $prefix . 'staff DROP COLUMN toplinemanager;';
        if (!$ci->db->query($alterTableSQL)) {
            die('Error altering table: ' . $ci->db->error()['message']);
        }
        $messages[] = 'Column "toplinemanager" dropped successfully.';
    } else {
        $messages[] = 'Column "toplinemanager" does not exist.';
    }
} else {
    die('Error: Table ' . $prefix . 'staff does not exist.');
}

// Check if the 'questions' table exists and drop it if so
if ($ci->db->table_exists($prefix . 'questions')) {
    if (!$ci->db->query('DROP TABLE ' . $prefix . 'questions')) {
        die('Error dropping table: ' . $ci->db->error()['message']);
    }
    $messages[] = 'Table "questions" dropped successfully.';
} else {
    $messages[] = 'Table "questions" does not exist.';
}

// Check if the 'huddlertodos' table exists and drop it if so
if ($ci->db->table_exists($prefix . 'huddlertodos')) {
    if (!$ci->db->query('DROP TABLE ' . $prefix . 'huddlertodos')) {
        die('Error dropping table: ' . $ci->db->error()['message']);
    }
    $messages[] = 'Table "huddlertodos" dropped successfully.';
} else {
    $messages[] = 'Table "huddlertodos" does not exist.';
}

// Check if the 'huddleraction_logs' table exists and drop it if so
if ($ci->db->table_exists($prefix . 'huddleraction_logs')) {
    if (!$ci->db->query('DROP TABLE ' . $prefix . 'huddleraction_logs')) {
        die('Error dropping table: ' . $ci->db->error()['message']);
    }
    $messages[] = 'Table "huddleraction_logs" dropped successfully.';
} else {
    $messages[] = 'Table "huddleraction_logs" does not exist.';
}

// Check if the 'tblnegativepoint' table exists and drop it if so
if ($ci->db->table_exists($prefix . 'negativepoint')) {
    if (!$ci->db->query('DROP TABLE ' . $prefix . 'negativepoint')) {
        die('Error dropping table: ' . $ci->db->error()['message']);
    }
    $messages[] = 'Table "tblnegativepoint" dropped successfully.';
} else {
    $messages[] = 'Table "tblnegativepoint" does not exist.';
}


// Output all messages
// foreach ($messages as $message) {
//     echo $message . "<br>";
// }
