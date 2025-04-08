<?php

defined('BASEPATH') or exit('No direct script access allowed');
// Load the database library
$CI = &get_instance();
$CI->load->database();

if (!$CI->db->table_exists(db_prefix() . 'emails')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "emails` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `thread_id` int(11) NOT NULL,
        `recipient` varchar(100) NOT NULL,
        `subject` varchar(255) NOT NULL,
        `message` text NOT NULL,
        `type` enum('received', 'sent') NOT NULL,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'threads')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "threads` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `employee_id` int(11) NOT NULL,
        `full_name` varchar(100) NOT NULL,
        `company` varchar(100) NOT NULL,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'employees')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "employees` (
        `emp_id` int(11) NOT NULL AUTO_INCREMENT,
        `full_name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `phone` varchar(20) NOT NULL,
        `alternate_phone` varchar(20) NULL,
        `image` varchar(255) NULL,
        `birth_date` date NULL,
        `address` text NULL,
        `organisation` varchar(100) NULL,
        `roles` varchar(50) NULL,
        `social_media` text NULL,
        PRIMARY KEY (`emp_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
