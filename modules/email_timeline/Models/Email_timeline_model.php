<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Email_timeline_model extends App_Model
{
    public function get_all_threads()
    {
        return $this->db->get(db_prefix() . 'emails')->result_array();
    }

    public function get_emails_by_thread($thread_id)
    {
        $this->db->where('thread_id', $thread_id);
        return $this->db->get(db_prefix() . 'emails')->result_array();
    }

    public function save_email($data)
    {
         // Convert arrays to strings if they exist
        if (isset($data['cc']) && is_array($data['cc'])) {
            $data['cc_email'] = implode(', ', $data['cc']);
            unset($data['cc']); // Remove the original 'cc' key
        }

        if (isset($data['bcc']) && is_array($data['bcc'])) {
            $data['bcc_email'] = implode(', ', $data['bcc']);
            unset($data['bcc']); // Remove the original 'bcc' key
        }
        $this->db->insert(db_prefix() . 'emails', $data);
    }
}
