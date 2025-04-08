<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Contact_timeline_model extends App_Model
{
   
    public function new_contact($data)
    {
        print_r($data);
        exit();
        $this->db->insert(db_prefix() . 'timelinecontacts', $data);
    }
}