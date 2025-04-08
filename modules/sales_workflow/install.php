<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Load the database library
$CI = &get_instance();
$CI->load->database();

// Check if the necessary tables exist, if not, create them

// Sales Workflow table
if (!$CI->db->table_exists(db_prefix() . 'sales_workflow_tasks')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "sales_workflow_tasks` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->table_exists(db_prefix() . 'sales_leads')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "sales_leads` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `source` varchar(100) NOT NULL,
        `type` varchar(100) NOT NULL,
        `status` varchar(50) NOT NULL,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->table_exists(db_prefix() . 'sales_tasks')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "sales_tasks` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `lead_id` int(11) NOT NULL,
        `task` varchar(255) NOT NULL,
        `assigned_to` int(11) NOT NULL,
        `due_date` datetime NOT NULL,
        `completed` tinyint(1) NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`lead_id`) REFERENCES " . db_prefix() . "sales_leads(`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->table_exists(db_prefix() . 'sales_opportunities')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "sales_opportunities` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `lead_id` int(11) NOT NULL,
        `stage` varchar(50) NOT NULL,
        `value` decimal(10,2) NOT NULL,
        `close_date` date NOT NULL,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`lead_id`) REFERENCES " . db_prefix() . "sales_leads(`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->table_exists(db_prefix() . 'sales_followups')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "sales_followups` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `opportunity_id` int(11) NOT NULL,
        `followup_date` date NOT NULL,
        `status` varchar(50) NOT NULL,
        `notes` text,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`opportunity_id`) REFERENCES " . db_prefix() . "sales_opportunities(`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

