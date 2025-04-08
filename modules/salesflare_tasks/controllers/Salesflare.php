<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Salesflare extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('salesflare_tasks_model');
    }
    public function index()
    {
        if (!has_permission('salesflare_tasks', '', 'view')) {
            access_denied('salesflare_tasks');
        }
        $data['title'] = _l('tasks');
        $this->load->view('tasks', $data);
    }
    public function manage()
    {
        if (!has_permission('salesflare_tasks', '', 'view')) {
            access_denied('salesflare_tasks');
        }
        $data['title'] = _l('manage');
        $this->load->view('manage', $data);
    }
    public function settings()
    {
        if (!has_permission('salesflare_tasks', '', 'view')) {
            access_denied('salesflare_tasks');
        }
        $data['title'] = _l('settings');
        $this->load->view('settings', $data);
    }
    public function tasks()
    {
        // if (!has_permission('salesflare_tasks', '', 'view')) {
        //     access_denied('salesflare_tasks');
        // }
        // $this->app->get_table_data('tasks');

        $this->load->view('tasks');
    }

    public function add()
    {
        if (!has_permission('salesflare_tasks', '', 'create')) {
            access_denied('salesflare_tasks');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $id = $this->salesflare_tasks_model->add($data);
            if ($id) {
                set_alert('success', _l('added_successfully', _l('task')));
                redirect(admin_url('salesflare/tasks'));
            }
        }
        $data['title'] = _l('add_new', _l('task'));
        $this->load->view('task', $data);
    }

}