<?php

class Ssalesflare_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_tasks()
    {
        $this->db->select('*');
        $this->db->from('tblsalesflare_tasks');
        $this->db->order_by('id', 'desc');
        return $this->db->get()->result_array();
    }
    public function get_task($id)
    {
        $this->db->select('*');
        $this->db->from('tblsalesflare_tasks');
        $this->db->where('id', $id);
        return $this->db->get()->row();
    }
    public function add($data)
    {
        $this->db->insert('tblsalesflare_tasks', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }
        return false;
    }
    public function update($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblsalesflare_tasks', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function delete($id)
    {
        $task = $this->get_task($id);
        $this->db->where('id', $id);
        $this->db->delete('tblsalesflare_tasks');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
}