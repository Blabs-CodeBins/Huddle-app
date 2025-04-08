<?php

class QuestionsModel extends App_Model
{
    // Table name
    private $table = 'questions';

    // Fillable fields
    protected $fillable = [
        'category',
        'question_text',
        'help_text',
        'options'
    ];

    // Constructor
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Get all questions
    public function get_all() {
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    // Get question by ID
    public function get_by_id($id) {
        $query = $this->db->get_where($this->table, array('id' => $id));
        return $query->row_array();
    }

    // Insert a new question
    public function insert($data) {
        // Filter data to include only fillable fields
        $filtered_data = array_intersect_key($data, array_flip($this->fillable));
        return $this->db->insert($this->table, $filtered_data);
    }

    // Update a question
    public function update($id, $data) {
        // Filter data to include only fillable fields
        $filtered_data = array_intersect_key($data, array_flip($this->fillable));
        $this->db->where('id', $id);
        return $this->db->update($this->table, $filtered_data);
    }

    // Delete a question
    public function delete($id) {
        return $this->db->delete($this->table, array('id' => $id));
    }

    public function update_status($question_ids, $status) {
        $this->db->where_in('id', $question_ids);
        $this->db->update('questions', ['active' => $status]);
    }
    
    public function update_status_for_nonselected($selected_ids, $status) {
        $this->db->where_not_in('id', $selected_ids);
        $this->db->update('questions', ['active' => $status]);
    }

    public function get_by_category_and_status($category, $status) {
        $this->db->where('category', $category); // Filter by category
        $this->db->where('active', 1);    // Filter by status
        $query = $this->db->get('questions');   // Ensure 'questions' is your table name
        return $query->result_array();
    }
    
}
    