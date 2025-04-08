<?php

defined('BASEPATH') or exit('No direct script access allowed');
$messages = [];
// Check if the database prefix function is available and return the correct prefix
if (!function_exists('db_prefix')) {
    die('Error: db_prefix() function is not defined.');
}

$prefix = db_prefix();
$ci =& get_instance(); // Get the CodeIgniter instance

// Check if the 'staff' table exists
if ($ci->db->table_exists($prefix . 'staff')) {
    // Check if the 'reports_to' column exists
    $column_exists_query = "SHOW COLUMNS FROM `" . $prefix . 'staff` LIKE \'reports_to\'';
    $column_exists_result = $ci->db->query($column_exists_query);

    if ($column_exists_result->num_rows() == 0) {
        // SQL Query to add the 'reports_to' column
        $alterTableSQL = 'ALTER TABLE `' . $prefix . 'staff`
            ADD `reports_to` int(10) DEFAULT NULL AFTER `role`;';

        // Execute the query
        if (!$ci->db->query($alterTableSQL)) {
            // Log or display the error
            die('Error altering table: ' . $ci->db->error()['message']);
        }

         $messages[] = 'Column "reports_to" added successfully.';
    } else {
         $messages[] = 'Column "reports_to" already exists.';
    }

    // Check if the 'todolist_pm' column exists
    $column_exists_query = "SHOW COLUMNS FROM `" . $prefix . 'staff` LIKE \'todolist_pm\'';
    $column_exists_result = $ci->db->query($column_exists_query);

    if ($column_exists_result->num_rows() == 0) {
        // SQL Query to add the 'todolist_pm' column
        $alterTableSQL = 'ALTER TABLE `' . $prefix . 'staff`
            ADD `todolist_pm` TINYINT(1) DEFAULT 0 AFTER `reports_to`;';

        // Execute the query
        if (!$ci->db->query($alterTableSQL)) {
            // Log or display the error
            die('Error altering table: ' . $ci->db->error()['message']);
        }

         $messages[] = 'Column "todolist_pm" added successfully.';
    } else {
         $messages[] = 'Column "todolist_pm" already exists.';
    }

    // Add 'toplinemanager' column if it does not exist
    $column_exists_query = "SHOW COLUMNS FROM `" . $prefix . 'staff` LIKE \'toplinemanager\'';
    $column_exists_result = $ci->db->query($column_exists_query);

    if ($column_exists_result->num_rows() == 0) {
        // SQL Query to add the 'toplinemanager' column
        $alterTableSQL = 'ALTER TABLE `' . $prefix . 'staff`
            ADD `toplinemanager` int(10) DEFAULT 0 AFTER `todolist_pm`;';

        // Execute the query
        if (!$ci->db->query($alterTableSQL)) {
            // Log or display the error
            die('Error altering table: ' . $ci->db->error()['message']);
        }

         $messages[] = 'Column "toplinemanager" added successfully.';
    } else {
         $messages[] = 'Column "toplinemanager" already exists.';
    }
} else {
    die('Error: Table ' . $prefix . 'staff does not exist.');
}
// Check if the 'questions' table exists
if (!$ci->db->table_exists($prefix . 'questions')) {
    // SQL Query to create the 'questions' table
    $createTableSQL = 'CREATE TABLE `' . $prefix . 'questions` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `category` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
        `question_text` TEXT COLLATE utf8mb4_general_ci NOT NULL,
        `options` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
        `active` TINYINT(1) NOT NULL DEFAULT 1,
        `opposite_warning` TEXT COLLATE utf8mb4_general_ci DEFAULT NULL,
        `opposite_warning_trigger` VARCHAR(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';

    // Execute the query to create the 'questions' table
    if (!$ci->db->query($createTableSQL)) {
        // Log or display the error
        die('Error creating table: ' . $ci->db->error()['message']);
    }

     $messages[] = 'Table "questions" created successfully.';

    // Now, insert the provided data into the 'questions' table
    $insertSQL = "INSERT INTO `" . $prefix . "questions` (`id`, `category`, `question_text`, `options`, `active`, `opposite_warning`, `opposite_warning_trigger`) VALUES
    (1, 'On submission', 'Have you done the Unit Testing?', 'Yes / No', 1, 'Do the Unit Testing and ensure there are no bugs.', 'No'),
    (2, 'On submission', 'Are any issues found in your Unit Testing?', 'Yes / No', 1, 'Solve the bug and then submit.', 'Yes'),
    (3, 'On submission', 'Did you check for any issues in overall or any other aspects?', 'Yes / No', 1, 'Check for any issues in overall created with your work.', 'No'),
    (4, 'On submission', 'Did you find any issues post your work?', 'Yes / No', 1, 'Solve the issues and then submit.', 'Yes'),
    (5, 'On submission', 'Did you take backup of specific widget or multiple widgets?', 'Yes / No', 1, 'Please note: There is a penalty point for not taking a backup.', 'No'),
    (6, 'On submission', 'Did you take backup of your current finished code?', 'Yes / No', 1, 'Take backup and save in your respective folders in the server.', 'No'),
    (7, 'On submission', 'Loom Video/Screenshot url for proof of work to submit to client?', '[url]', 1, NULL, NULL),
    (8, 'Help Request', 'Define where you got stuck?', '[description box]', 1, NULL, NULL),
    (9, 'Help Request', 'Loom Video/Screenshot url if any.', '[url]', 1, NULL, NULL),
    (10, 'On Approve', 'Did you check thoroughly that this task is complete?', 'Yes / No', 1, NULL, NULL),
    (11, 'HR submission', 'Have all the issues related to the Help Request been resolved?', 'Yes / No', 1, NULL, NULL),
    (12, 'On Approve', 'Did you find any bugs?', 'Yes / No', 1, NULL, NULL),
    (13, 'On Approve', 'Is comment made in the task?', 'Yes / No', 1, 'Go to the task and comment.', 'No'),
    (14, 'New Work', 'Did you comment in task?', 'Yes / No', 1, 'Please comment and inform the client and select yes.', 'No'),
    (15, 'New Work', 'Did you message in skype group to sales?', 'Yes / No', 1, 'Please message in the Skype group to the sales team.', 'No'),
    (16, 'No Problem', 'You confirm that you have responded to client properly.', NULL, 1, NULL, NULL),
    (17, 'Bug Found', 'Would you like to assign a different employee?', 'Yes / No', 1, NULL, NULL);";

    // Execute the insert query
    if (!$ci->db->query($insertSQL)) {
        die('Error inserting data into questions table: ' . $ci->db->error()['message']);
    }

     $messages[] = 'Data inserted into "questions" table successfully.';
} else {
    $messages[] = 'Table "questions" already exists.';
}
// Check if the 'huddlertodos' table exists
if (!$ci->db->table_exists($prefix . 'huddlertodos')) {
    // SQL Query to create the 'huddlertodos' table
    $createTableSQL = 'CREATE TABLE `' . $prefix . 'huddlertodos` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `task_id` INT(11) NOT NULL,
        `staff_id` INT(11) NOT NULL,
        `task_name` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
        `description` TEXT COLLATE utf8mb4_general_ci DEFAULT NULL,
        `status` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
        `start_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,  
        `due_date` DATETIME DEFAULT CURRENT_TIMESTAMP,             
        `questions_data` LONGTEXT COLLATE utf8mb4_bin DEFAULT NULL,
        `todo_createdby` VARCHAR(111) COLLATE utf8mb4_general_ci NOT NULL,
        `date_added` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `helpreq_remarks` TEXT COLLATE utf8mb4_general_ci DEFAULT NULL,
        `video_link` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
        `helpreq_at` DATETIME DEFAULT NULL,
        `pm_remarks` TEXT COLLATE utf8mb4_general_ci DEFAULT NULL,
        `helpprovided_at` DATETIME DEFAULT NULL,
        `new_comment_by_client` INT(11) NOT NULL DEFAULT 0,
        `todo_completed` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'No\',
        `needs_attention_status` ENUM(\'Bug Found\',\'New Work\',\'No Problem\',\'Completed\') COLLATE utf8mb4_general_ci DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';


    // Execute the query to create the 'huddlertodos' table
    if (!$ci->db->query($createTableSQL)) {
        // Log or display the error
        die('Error creating table: ' . $ci->db->error()['message']);
    }

     $messages[] = 'Table "huddlertodos" created successfully.';
} else {
    $messages[] = 'Table "huddlertodos" already exists.';
}

// Check if the 'huddleraction_logs' table exists
if (!$ci->db->table_exists($prefix . 'huddleraction_logs')) {
    // SQL Query to create the 'huddleraction_logs' table
    $createTableSQL = 'CREATE TABLE `' . $prefix . 'huddleraction_logs` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `huddlertodo_id` INT(11) NOT NULL,
        `task_id` INT(55) NOT NULL,
        `staff_id` INT(11) NOT NULL,
        `staff_name` VARCHAR(200) COLLATE utf8mb4_general_ci NOT NULL,
        `todo_status` INT(11) NOT NULL,
        `comment` TEXT COLLATE utf8mb4_general_ci NOT NULL,
        `action_type` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
        `timestamp` DATETIME NOT NULL,
        `formdata_json` LONGTEXT COLLATE utf8mb4_bin DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';

    // Execute the query to create the 'huddleraction_logs' table
    if (!$ci->db->query($createTableSQL)) {
        // Log or display the error
        die('Error creating table: ' . $ci->db->error()['message']);
    }

     $messages[] = 'Table "huddleraction_logs" created successfully.';
} else {
    $messages[] = 'Table "huddleraction_logs" already exists.';
}

// Check if the 'tblnegativepoint' table exists
if (!$ci->db->table_exists($prefix . 'negativepoint')) {
    // SQL Query to create the 'tblnegativepoint' table
    $createTableSQL = 'CREATE TABLE `' . $prefix . 'negativepoint` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `todo_id` INT(11) DEFAULT NULL,
        `task_id` INT(11) DEFAULT NULL,
        `staff_id` INT(11) DEFAULT NULL,
        `staffname` VARCHAR(255) DEFAULT NULL,
        `reassigned_fromstaffid` INT(11) DEFAULT NULL,
        `reassignedfromname` VARCHAR(255) DEFAULT NULL,
        `task_point` VARCHAR(200) NOT NULL DEFAULT \'0\',
        `taskrejectioncount` INT(11) DEFAULT NULL,
        `negativepoint` VARCHAR(255) NOT NULL DEFAULT \'0\',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';

    // Execute the query to create the 'tblnegativepoint' table
    if (!$ci->db->query($createTableSQL)) {
        // Log or display the error
        die('Error creating table: ' . $ci->db->error()['message']);
    }

    $messages[] = 'Table "tblnegativepoint" created successfully.';
} else {
    $messages[] = 'Table "tblnegativepoint" already exists.';
}


// Output all messages (success and error)
// foreach ($messages as $message) {
//     echo $message . "<br>";
// }
