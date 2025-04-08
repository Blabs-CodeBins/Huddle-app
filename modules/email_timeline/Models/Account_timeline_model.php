<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Account_timeline_model extends App_Model
{
   
    public function new_account($data)
    {
        // print_r($data);
        // exit();
        $this->db->insert(db_prefix() . 'timelineaccounts', $data);
    }
}