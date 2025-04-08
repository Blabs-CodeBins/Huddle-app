<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

defined('BASEPATH') or exit('No direct script access allowed');

class Sales_workflow extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('sales_workflow_model');
        hooks()->do_action('sales_workflow_init');
    }

    public function index()
    {
        $data['title'] = _l('sales_workflow');
        $data['leads'] = $this->sales_workflow_model->get_all_leads();
        // print_r($data);
        // exit;
        $this->load->view('manage', $data);
    }

    /**
     * view opportunity
     * @return view
     */
    public function view_opportunity($id)
    {
        $data['opportunity'] = $this->sales_workflow_model->get_opportunity($id);
        $data['followups'] = $this->sales_workflow_model->get_followups($id);
        $this->load->view('sales_workflow/sales_workflow/view_opportunity', $data);
    }

    public function capture_lead() {
        $lead_data = [
            'source' => $this->input->post('source'),
            'type' => $this->input->post('type'),
            'status' => 'New',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $lead_id = $this->sales_workflow_model->add_lead($lead_data);
    
        $task_data = [
            'lead_id' => $lead_id,
            'task' => 'Send First Email',
            'assigned_to' => $this->session->userdata('staff_user_id'),
            'due_date' => date('Y-m-d H:i:s', strtotime('+1 minute')),
        ];
        $this->sales_workflow_model->add_task($task_data);
    
        redirect(admin_url('sales_workflow'));
    }

    public function send_first_email($task_id) {
        // Logic to send email
    
        // Mark task as complete
        $this->sales_workflow_model->update_task($task_id, ['completed' => 1]);
    
        // Create follow-up task
        $task_data = [
            'lead_id' => $this->input->post('lead_id'),
            'task' => 'Check for Customer Reply',
            'assigned_to' => $this->session->userdata('staff_user_id'),
            'due_date' => date('Y-m-d H:i:s', strtotime('+2 hours')),
        ];
        $this->sales_workflow_model->add_task($task_data);
    
        redirect(admin_url('sales_workflow'));
    }

    public function check_customer_reply($task_id) {
        // Logic to check for customer reply
    
        if ($customer_replied) {
            // Create tasks to reply to the customer
            $task_data1 = [
                'lead_id' => $this->input->post('lead_id'),
                'task' => 'Reply to Customer on Email',
                'assigned_to' => $this->session->userdata('staff_user_id'),
                'due_date' => date('Y-m-d H:i:s'),
            ];
            $this->sales_workflow_model->add_task($task_data1);
    
            $task_data2 = [
                'lead_id' => $this->input->post('lead_id'),
                'task' => 'Reply to Customer on WhatsApp',
                'assigned_to' => $this->session->userdata('staff_user_id'),
                'due_date' => date('Y-m-d H:i:s'),
            ];
            $this->sales_workflow_model->add_task($task_data2);
        } else {
            // Create task for WhatsApp follow-up
            $task_data = [
                'lead_id' => $this->input->post('lead_id'),
                'task' => 'WhatsApp Follow-up',
                'assigned_to' => $this->session->userdata('staff_user_id'),
                'due_date' => date('Y-m-d H:i:s'),
            ];
            $this->sales_workflow_model->add_task($task_data);
        }
    
        // Mark the current task as complete
        $this->sales_workflow_model->update_task($task_id, ['completed' => 1]);
    
        redirect(admin_url('sales_workflow'));
    }

    public function reports() {
        $data['title'] = _l('Sales Workflow Reports');
        $data['tasks'] = $this->sales_workflow_model->get_all_tasks();

        $this->load->view('sales_workflow/reports', $data);
    }
        
}
