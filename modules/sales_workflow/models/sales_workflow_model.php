<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

defined('BASEPATH') or exit('No direct script access allowed');

class Sales_workflow_model extends CI_Model
{
    public function get_all_leads()
    {
        return $this->db->get(db_prefix() . 'sales_leads')->result_array();
    }

    public function get_lead($id)
    {
        return $this->db->where('id', $id)->get(db_prefix() . 'sales_leads')->row();
    }

    public function get_opportunity($id)
    {
        return $this->db->where('id', $id)->get(db_prefix() . 'sales_opportunities')->row();
    }

    public function get_followups($opportunity_id)
    {
        return $this->db->where('opportunity_id', $opportunity_id)->get(db_prefix() . 'sales_followups')->result_array();
    }

    public function add_lead($data)
    {
        $this->db->insert(db_prefix() . 'sales_leads', $data);
        return $this->db->insert_id();
    }

    public function update_lead($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'sales_leads', $data);
        return $this->db->affected_rows();
    }

    public function delete_lead($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'sales_leads');
        return $this->db->affected_rows();
    }

    public function add_opportunity($data)
    {
        $this->db->insert(db_prefix() . 'sales_opportunities', $data);
        return $this->db->insert_id();
    }

    public function update_opportunity($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'sales_opportunities', $data);
        return $this->db->affected_rows();
    }

    public function delete_opportunity($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'sales_opportunities');
        return $this->db->affected_rows();
    }

    public function add_followup($data)
    {
        $this->db->insert(db_prefix() . 'sales_followups', $data);
        return $this->db->insert_id();
    }

    public function update_followup($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'sales_followups', $data);
        return $this->db->affected_rows();
    }

    public function delete_followup($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'sales_followups');
        return $this->db->affected_rows();
    }
}
