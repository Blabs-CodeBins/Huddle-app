<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Todo extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['staff_model', 'QuestionsModel', 'HuddlerTodosModel', 'Tasks_model', 'emails_model']);
        $this->load->library('form_validation');
        $this->load->helper('tasks_helper');
    }

    public function index()
    {
        return $this->mytodos();
    }

    /**
     * Fetch tasks by multiple staff IDs.
     */
    private function get_tasks_by_multiple_staff_ids($staffIds, $where = [])
    {
        if (!is_array($staffIds) || empty($staffIds)) {
            throw new InvalidArgumentException('Invalid or empty staff IDs array provided.');
        }

        $escapedIds = array_map([$this->db, 'escape_str'], $staffIds);
        $escapedIdsString = implode(',', $escapedIds);

        if (!empty($where)) {
            $this->db->where($where);
        }

        $this->db->where('id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid IN (' . $escapedIdsString . '))');
        return $this->db->get(db_prefix() . 'tasks')->result_array();
    }

    /**
     * Fetch tasks by IDs.
     */
    private function get_tasks_by_ids($ids, $where = [])
    {
        if (empty($ids)) {
            return [];
        }

        $this->db->where($where);
        $this->db->where_in('id', $ids);
        return $this->db->get(db_prefix() . 'tasks')->result_array();
    }

    /**
     * Handle AJAX request to fetch tasks by IDs.
     */
    public function GetTaskbyIds()
    {
        if (!$this->input->is_ajax_request()) {
            $this->send_error_response('Not a valid request');
            return;
        }

        $ids = $this->input->post('task_ids');
        $pmId = $this->input->post('pmId');
        $staffId = $pmId ?: $this->session->userdata('staff_user_id');

        $tasks = is_array($ids) ? $this->get_tasks_by_ids($ids) : $this->get_single_task($ids);
        $projectsById = $this->get_all_projects();

        foreach ($tasks as &$task) {
            $task['project_name'] = $this->get_project_name($task, $projectsById);
            $task['img_tag'] = $this->generate_staff_image_tag($staffId);
        }

        $this->send_success_response(['tasks' => $tasks]);
    }

    /**
     * Create today's plan.
     */
    public function create_todays_plan()
    {
        if (!$this->input->is_ajax_request()) {
            $this->send_error_response('Not a valid request');
            return;
        }

        $ids = $this->input->post('task_ids');
        $removeTaskIds = $this->input->post('remove_task_ids');
        $pmId = $this->input->post('pmId');

        if (!is_array($ids)) {
            $this->send_error_response('Assign at least one task from Starts Today');
            return;
        }

        $staffId = $pmId ?: $this->session->userdata('staff_user_id');
        $createdBy = $pmId ? 'PM/TL' : 'self';

        $this->handle_task_removal($staffId, $removeTaskIds);

        $tasks = $this->get_tasks_by_ids($ids);
        $existingTaskIds = $this->HuddlerTodosModel->get_existing_task_ids($staffId, $ids);
        $insertData = $this->prepare_new_tasks($tasks, $existingTaskIds, $staffId, $createdBy);

        if (!empty($insertData)) {
            $this->HuddlerTodosModel->insertMultiple($insertData);
            $this->log_and_notify($staffId, $createdBy, $insertData, $ids);
            $this->send_success_response('Today\'s plan saved successfully');
        } else {
            $this->send_warning_response('No new tasks assigned or deleted.');
        }
    }

    /**
     * Handle task removal.
     */
    private function handle_task_removal($staffId, $removeTaskIds)
    {
        if (is_array($removeTaskIds) && !empty($removeTaskIds)) {
            $this->HuddlerTodosModel->delete_tasks($staffId, $removeTaskIds);
        }
    }

    /**
     * Prepare new tasks for insertion.
     */
    private function prepare_new_tasks($tasks, $existingTaskIds, $staffId, $createdBy)
    {
        $insertData = [];
        foreach ($tasks as $task) {
            if (!in_array($task['id'], $existingTaskIds)) {
                $insertData[] = [
                    'task_id' => $task['id'],
                    'staff_id' => $staffId,
                    'task_name' => $task['name'],
                    'due_date' => $task['duedate'],
                    'start_date' => $task['startdate'],
                    'status' => 2,
                    'todo_createdby' => $createdBy,
                    'date_added' => date('Y-m-d H:i:s'),
                ];
            }
        }
        return $insertData;
    }

    /**
     * Log actions and send notifications.
     */
    private function log_and_notify($staffId, $createdBy, $insertData, $taskIds)
    {
        $logData = $this->prepare_log_data($staffId, $createdBy);
        $logActionComment = $this->generate_log_action_comment('assign_self', $logData);

        $this->logAction('assign_self', $logActionComment, '', implode(',', $taskIds), $staffId, get_staff_full_name($staffId), 2, $insertData);
        $this->SendSODEmails($staffId, false, $createdBy);
    }

    /**
     * Send SOD emails.
     */
    private function SendSODEmails($staffId, $isModified, $createdBy)
    {
        $tasks = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id($staffId);
        $organizedData = $this->organizeHuddlerTodo($tasks);

        $employee = $this->staff_model->get($staffId, ['active' => 1]);
        $manager = $this->staff_model->get($employee->reports_to, ['active' => 1]);

        if ($createdBy === 'self') {
            $this->send_employee_sod_email($employee, $manager, $organizedData, $isModified);
        } else {
            $this->send_manager_sod_email($employee, $manager, $organizedData);
        }
    }

    /**
     * Send SOD email to employee.
     */
    private function send_employee_sod_email($employee, $manager, $organizedData, $isModified)
    {
        $subject = $isModified ? "Modified Work Plan" : "Start of Day Report Submitted";
        $template = $isModified ? $this->createModifiedWorkPlanEmailForEmployee($employee->firstname, date('M jS, Y, \a\t h:i:s A T'), $organizedData) : $this->createSODEmailForEmployee($employee->firstname, date('M jS, Y'), $organizedData);

        $this->emails_model->send_simple_email($employee->email, $subject, $template);
    }

    /**
     * Send SOD email to manager.
     */
    private function send_manager_sod_email($employee, $manager, $organizedData)
    {
        $subject = "Start of Day Report Submitted by {$employee->firstname}";
        $template = $this->createSODEmailForManager($manager->firstname, $employee->firstname, $employee->staffid, date('M jS, Y'), $organizedData);

        $this->emails_model->send_simple_email($manager->email, $subject, $template);
    }

    /**
     * Send success response.
     */
    private function send_success_response($data)
    {
        echo json_encode(['status' => 'success', 'data' => $data]);
        exit;
    }

    /**
     * Send error response.
     */
    private function send_error_response($message)
    {
        echo json_encode(['status' => 'error', 'message' => $message]);
        exit;
    }

    /**
     * Send warning response.
     */
    private function send_warning_response($message)
    {
        echo json_encode(['status' => 'warning', 'message' => $message]);
        exit;
    }

    // Additional helper methods can be added here...
}