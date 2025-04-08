<?php
class HuddlerTodosModel extends App_Model
{
    protected $table = 'tblhuddlertodos'; // Name of your database table
    protected $primaryKey = 'id'; // Primary key of the table

    protected $allowedFields = [
        'task_id',
        'staff_id',
        'task_name',
        'description',
        'status',
        'due_date',
        'date_added'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Insert multiple tasks into the HuddlerTodos table
     * @param array $data Array of tasks to be inserted
     * @return bool Whether the insertion was successful
     */
    public function insertMultiple($data)
    {
        if (empty($data)) {
            return false;
        }

        $this->db->insert_batch($this->table, $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
    
    /**
     * Get HuddlerTodos by staff_id(s)
     * 
     * @param mixed $staffId The staff ID or an array of staff IDs
     * @param array $where Additional where conditions (optional)
     * @return array List of HuddlerTodos
     */
    public function get_huddler_todos_by_staff_id($staffId, $where = [])
    {
        // Apply additional where conditions if any
        if (!empty($where)) {
            $this->db->where($where);
        }

        // Check if $staffId is an array or a single value
        if (is_array($staffId)) {
            // Escape each staff ID and use the IN clause
            $staffId = array_map([$this->db, 'escape_str'], $staffId);
            $this->db->where_in('staff_id', $staffId);
        } else {
            // Escape the single staff ID
            $this->db->where('staff_id', $this->db->escape_str($staffId));
        }

        // Fetch and return the result set as an array
        return $this->db->get($this->table)->result_array();
    }

    public function has_todays_todo_submitted($staffId) {
        $this->db->where('staff_id', $staffId);
        $this->db->where('DATE(date_added) = CURDATE()', null, false);
       // $this->db->where_in('status', [2, 6, 5, 3]);  // Corrected this line
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }
    public function get_existing_task_ids($staffId, $taskIds)
    {
        $this->db->select('task_id');
        $this->db->from($this->table);
        $this->db->where('staff_id', $staffId);
        $this->db->where_in('task_id', $taskIds);
        $query = $this->db->get();

        return $query->result_array();
    }
    public function update($data, $id)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }
    public function get_status_by_id($id)
    {
        $this->db->select('status');
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->status;
        } else {
            return null;
        }
    }
    public function getHelpRequestDetails($id)
    {
        $this->db->select('id, staff_id, status, helpreq_remarks, video_link, helpreq_at, pm_remarks, helpprovided_at');
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();

    }
    public function get_answers($taskid, $status)
    {
        $this->db->select('id, task_id, questions_data');
        $this->db->from($this->table);
        $this->db->where('task_id', $taskid);
        $this->db->where('status', $status);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function delete_tasks($staffId, $taskIds)
    {
        if (!is_array($taskIds)) {
            return false;
        }
        $this->db->where('staff_id', $staffId);
        $this->db->where_in('task_id', $taskIds);
        $this->db->delete($this->table);
        return $this->db->affected_rows() > 0;
    }

    public function get_staffid_by_taskid($taskId)
    {
        $this->db->select('staff_id');
        $this->db->from($this->table);
        $this->db->where('task_id', $taskId);
        $query = $this->db->get();
        return $query->row_array();
    }
 
    




}
