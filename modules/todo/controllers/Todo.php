<?php defined('BASEPATH') or exit('No direct script access allowed');

class Todo extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        
        // Load any necessary models and libraries here...
        $this->load->model('staff_model'); // Ensure this is loaded as well
        $this->load->model('QuestionsModel');
        $this->load->model('HuddlerTodosModel');
        $this->load->model('Tasks_model');
        $this->load->model('emails_model');
        $this->load->library('form_validation');
        $this->load->helper('tasks_helper');
    }

    public function index()
    {
        return $this->huddlerSOD();
    }
    public function huddlerSOD(){
        $staffId = $this->session->userdata('staff_user_id');
        $staff   = $this->staff_model->get($staffId);
        if (!$staffId) {
            show_error('Unauthorized Access', 403);
            return;
        }
        // Check if SOD already submitted
        // if ($this->HuddlerTodosModel->has_todays_todo_submitted($staffId)) {
        //     return $this->mytodos();
        // }
    
        // Initialize essential staff data
        $data = [
            'staffid'          => $staff->staffid,
            'staffemail'       => $staff->email,
            'staffull_name'    => $staff->full_name,
            'staffull_profile' => $staff->profile_image,
            'reports_to'       => $staff->reports_to,
            'todolist_pm'      => $staff->todolist_pm
        ];
        
        // Get reporting users and determine roles
        $reportingUsers = $this->staff_model->get('', ['reports_to' => $staffId], '');
        $isProjectManager = ($staff->todolist_pm == 1) && !empty($reportingUsers);
        $isToplineManager = ($staff->todolist_pm == 0) && ($staff->toplinemanager == 1);

        if($isToplineManager){

            // Function to get all subordinates recursively
            function getAllSubordinates($staffId, $staff_model) {
                $subordinates = $staff_model->get('', ['reports_to' => $staffId], '');
                $allSubordinates = [];

                foreach ($subordinates as $subordinate) {
                    // Add this subordinate
                    $allSubordinates[] = $subordinate['staffid'];
                    // Get the subordinates of this subordinate recursively
                    $subordinateIds = getAllSubordinates($subordinate['staffid'], $staff_model);
                    $allSubordinates = array_merge($allSubordinates, $subordinateIds);
                }
                
                return $allSubordinates;
            }
            // Collect all staff IDs for the query, including the current user
            $staffIds = array_map(function ($user) {
                return $user['staffid'];
            }, $reportingUsers);

            if ($isToplineManager) {
                $allSubordinateIds = getAllSubordinates($staffId, $this->staff_model);
                $staffIds = $allSubordinateIds;
                $reportingUsers = $this->staff_model->get('', 'staffid IN (' . implode(',', $staffIds) . ')');
            }
            
            // Get tasks and todos for all relevant staff IDs
            $allTasks = $this->get_tasks_by_multiple_staff_ids($staffIds, [], $isToplineManager);
            $allHuddlerTodos = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id($staffIds);
        } else {
            // If not a Project Manager, get tasks and todos only for the current staff ID
            $allTasks = $this->tasks_model->get_tasks_by_staff_id($staffId);
            $allHuddlerTodos = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id($staffId);
        }
      
    
        // Update and organize todos
        $updatedTodos = $this->updateHuddlerTodos($allTasks, $allHuddlerTodos);
        $organizedData = $this->organizeHuddlerTodo($updatedTodos);
        if ($isToplineManager) {
            $organizedData['todaysToDo'] = array_filter($organizedData['todaysToDo'], function ($todo) use ($staffId) {
                return $todo['staff_id'] == $staffId; // Only include TL's own todos
            });
        }
  
        
        // Filter tasks not in Huddler Todos
        $huddlerTaskIds = array_column($allHuddlerTodos, 'task_id');
        $filteredTasks = array_filter($allTasks, fn($task) => !in_array($task['id'], $huddlerTaskIds));
        $organizedTasks = $this->organizeTasksByDate($filteredTasks);
    
        // Merge organized data and additional flags
        $data = array_merge($data, $organizedTasks, $organizedData, [
            'isProjectManager' => $isProjectManager,
            'istoplinemanager' => $isToplineManager,
            'reportingUsers'   => $isProjectManager ? $reportingUsers : [],
        ]);
        
        
        // Get quantitative report if available
        if ($quantitativeReport = $this->GetStaffTaskPoints($staffId)) {
            $data['QuantitativeReport'] = $quantitativeReport;
        }
        $data['countdata'] = array_sum(array_map('count', array_intersect_key($data, array_flip(['dueToday', 'dueNext5Days', 'dueNext25Days', 'overdueTask', 'todaysToDo']))));

        if ($this->input->is_ajax_request()) {
            set_alert('danger', 'Access Denied');
            return;
        }
        // echo "<pre>"; print_r($data); echo "</pre>"; exit();
        $this->load->view('todo_todays_plan', $data);
       // $this->mytodos();
    }
    public function getReports($staffId = '') {
        $staffId = !empty($staffId) ? $staffId : $this->session->userdata('staff_user_id');
        $staff = $this->staff_model->get($staffId);
        $reportingUsers = $this->staff_model->get('', ['reports_to' => $staffId], '');
    
        $isProjectManager = $staff->todolist_pm == 1 && !empty($reportingUsers);
        $isToplineManager = $staff->toplinemanager == 1 && !empty($reportingUsers);
    
        function getAllSubordinates($id, $model) {
            $subordinates = $model->get('', ['reports_to' => $id], '');
            $ids = [];
    
            foreach ($subordinates as $sub) {
                $ids[] = $sub['staffid'];
                $ids = array_merge($ids, getAllSubordinates($sub['staffid'], $model));
            }
            return $ids;
        }
    
        if ($isToplineManager) {
            $staffIds = getAllSubordinates($staffId, $this->staff_model);
            $reportingUsers = $this->staff_model->get('', 'staffid IN (' . implode(',', $staffIds) . ')');
        }
    
        if (is_admin($staffId)) {
            $allStaff = $this->staff_model->get('', ['active' => 1]);
            $staffIds = array_column(array_filter($allStaff, fn($s) => $s['staffid'] != $staffId ), 'staffid');
            $reportingUsers = $this->staff_model->get('', 'staffid IN (' . implode(',', $staffIds) . ')');
        }

        $data = [
            'reportingUsers' => ($isToplineManager || $isProjectManager || is_admin()) ? $reportingUsers : [],
            'QuantitativeReport' => $this->GetStaffTaskPoints($staffId) ?? [],
            'QualitativeReport' => $this->GetStaffQualitativeReport($staffId) ?? [],
            'staffid' => $staffId,
            'isProjectManager' => $isProjectManager,
            'istoplinemanager' => $isToplineManager,
        ];
        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => 'success', 'data' => $data]);
            exit();
        }
       
        $this->load->view('reports', $data);
    }
    
    public function mytodos()
    {
        return $this->ViewMyTodos();
    }

    public function EditMyTodos()
    {
        $staffId = $this->session->userdata('staff_user_id');
        $staff   = $this->staff_model->get($staffId);
    
        // Initialize essential staff data
        $data = [
            'staffid'          => $staff->staffid,
            'staffemail'       => $staff->email,
            'staffull_name'    => $staff->full_name,
            'staffull_profile' => $staff->profile_image,
            'reports_to'       => $staff->reports_to,
            'todolist_pm'      => $staff->todolist_pm,
        ];
    
        // Get reporting users and determine roles
        $reportingUsers = $this->staff_model->get('', ['reports_to' => $staffId], '');
        $isProjectManager = ($staff->todolist_pm == 1) && !empty($reportingUsers);
        $isToplineManager = ($staff->todolist_pm == 1) && ($staff->toplinemanager == 1);
    
        // Retrieve tasks and todos
        $allTasks = $this->tasks_model->get_tasks_by_staff_id($staffId);
        $allHuddlerTodos = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id($staffId);
    
        // Update and organize todos
        $updatedTodos = $this->updateHuddlerTodos($allTasks, $allHuddlerTodos);
        $organizedData = $this->organizeHuddlerTodo($updatedTodos);
    
        // Filter tasks not in Huddler Todos
        $huddlerTaskIds = array_column($allHuddlerTodos, 'task_id');
        $filteredTasks = array_filter($allTasks, fn($task) => !in_array($task['id'], $huddlerTaskIds));
        $organizedTasks = $this->organizeTasksByDate($filteredTasks);
    
        // Merge organized data and additional flags
        $data = array_merge($data, $organizedTasks, $organizedData, [
            'isProjectManager' => $isProjectManager,
            'istoplinemanager' => $isToplineManager,
            'reportingUsers'   => $isProjectManager ? $reportingUsers : [],
        ]);
    
        // Get quantitative report if available
        if ($quantitativeReport = $this->GetStaffTaskPoints($staffId)) {
            $data['QuantitativeReport'] = $quantitativeReport;
        }

        // Load view with data
        $this->load->view('mytodos', $data);
    }
    
    public function ViewMyTodos()
    {
        $staffId = $this->session->userdata('staff_user_id');
        $staff   = $this->staff_model->get($staffId);
        
        // Initialize essential staff data
        $data = [
            'staffid'           => $staff->staffid,
            'staffemail'        => $staff->email,
            'staffull_name'     => $staff->full_name,
            'staffull_profile'  => $staff->profile_image,
            'reports_to'        => $staff->reports_to,
            'todolist_pm'       => $staff->todolist_pm,
        ];

        // Get all users reporting to the current staff member
        $reportingUsers = $this->staff_model->get('', ['reports_to' => $staffId], '');

        // Check if the user is a Project Manager or Topline Manager
        $isProjectManager = ($staff->todolist_pm == 1) && !empty($reportingUsers);
        $isToplineManager = ($staff->todolist_pm == 1) && ($staff->toplinemanager == 1);

        // Retrieve tasks and todos assigned to the user
        $allTasks = $this->tasks_model->get_tasks_by_staff_id($staffId);
        $allHuddlerTodos = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id($staffId);

        // Update and organize todos with relevant task data
        $updatedTodos = $this->updateHuddlerTodos($allTasks, $allHuddlerTodos);
        $organizedData = $this->organizeHuddlerTodo($updatedTodos);

        // Assign organized data and role flags to view data
        $data = array_merge($data, $organizedData, [
            'isProjectManager' => $isProjectManager,
            'istoplinemanager' => $isToplineManager,
        ]);

        if ($isProjectManager) {
            $data['reportingUsers'] = $reportingUsers;
        }

        // Pass data to the view
        $this->load->view('mytodo_view', $data);
    }

    public function myteamtodos()
    {
        $staffId = $this->session->userdata('staff_user_id');
        $staff = $this->staff_model->get($staffId); // Get current staff data
        // Prepare initial staff data
        $data = [
            'staffid'         => $staff->staffid,
            'staffemail'      => $staff->email,
            'staffull_name'   => $staff->full_name,
            'staffull_fname'  => $staff->firstname,
            'staffull_profile'=> $staff->profile_image,
            'reports_to'      => $staff->reports_to,
            'todolist_pm'     => $staff->todolist_pm
        ];
        $data['staffMembers'] = $this->staff_model->get('', ['active' => 1]);
        // Retrieve all users that report to the current user
        $reportingUsers = $this->staff_model->get('',['reports_to' =>$staffId],'');

        // Check if the current user is a Project Manager
        $isProjectManager = ($staff->todolist_pm == 1) && !empty($reportingUsers);
        $istoplinemanager = ($staff->toplinemanager == 1) && !empty($reportingUsers);

        // Function to get all subordinates recursively
        function getAllSubordinates($staffId, $staff_model) {
            $subordinates = $staff_model->get('', ['reports_to' => $staffId], '');
            $allSubordinates = [];

            foreach ($subordinates as $subordinate) {
                // Add this subordinate
                $allSubordinates[] = $subordinate['staffid'];
                // Get the subordinates of this subordinate recursively
                $subordinateIds = getAllSubordinates($subordinate['staffid'], $staff_model);
                $allSubordinates = array_merge($allSubordinates, $subordinateIds);
            }
            
            return $allSubordinates;
        }

        // Collect all staff IDs for the query
        $staffIds = array_map(function ($user) {
            return $user['staffid'];
        }, $reportingUsers);

        // Include all subordinates if the user is a top-line manager
        if ($istoplinemanager) {
            $allSubordinateIds = getAllSubordinates($staffId, $this->staff_model);
            $staffIds = $allSubordinateIds;
            $reportingUsers = $this->staff_model->get('', 'staffid IN (' . implode(',', $staffIds) . ')');
        }
    
        if (is_admin($staffId)) {
            $allStaff = $this->staff_model->get('', ['active' => 1]);
            $staffIds = array_column(array_filter($allStaff, fn($s) => $s['staffid'] != $staffId ), 'staffid');
            $reportingUsers = $this->staff_model->get('', 'staffid IN (' . implode(',', $staffIds) . ')');
        }
       

        
        // Include the current user's ID if they are a Project Manager
        if ($isProjectManager || $istoplinemanager || is_admin($staffId)) {
            $staffIds[] = $staffId;
        } else {
            set_alert('danger', 'Access Denied');
            $previousUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : admin_url('todo/mytodos');
            redirect($previousUrl);
        }
        $staffIds = array_unique($staffIds);
      
        // Retrieve tasks assigned to the collected staff IDs
        $allTasks        = $this->get_tasks_by_multiple_staff_ids($staffIds);
        $allHuddlerTodos = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id($staffIds);
        // echo $this->db->last_query().'/n';
        // echo "<pre>"; print_r($staffIds); echo "</pre>"; exit();
        // Update and organize todos with relevant task data
        $allhuddlerTodos = $this->updateHuddlerTodos($allTasks, $allHuddlerTodos);
        $organizeData    = $this->organizeHuddlerTodo($allhuddlerTodos);

         // Merge organized data and role flags into the view data
         $data = array_merge($data, $organizeData, [
            'isProjectManager'   => $isProjectManager,
            'istoplinemanager'   => $istoplinemanager,
            'reportingUsers'     => ($istoplinemanager || $isProjectManager || is_admin()) ? $reportingUsers : [],
            'allSubordinateIds'  => $istoplinemanager ? $allSubordinateIds : []
        ]);
        
        // Add quantitative report if available
        if ($quantReport = $this->GetStaffTaskPoints($staffId)) {
            $data['QuantitativeReport'] = $quantReport;
        }
        // print_r($data);
        // exit();
        // Pass the data to the view
        $this->load->view('myteamtodo_view', $data);
    }
    
    public function myteamtodos_edit($staffId) 
    {
        $currentUserId = $this->session->userdata('staff_user_id');
        $staff = $this->staff_model->get($staffId); // Get current staff data

        // Prepare initial staff data
        $data = [
            'staffid'         => $staff->staffid,
            'staffemail'      => $staff->email,
            'staffull_name'   => $staff->full_name,
            'staffull_fname'  => $staff->firstname,
            'staffull_profile'=> $staff->profile_image,
            'reports_to'      => $staff->reports_to,
            'todolist_pm'     => $staff->todolist_pm
        ];
       
        // Retrieve all users that report to the current user
        $reportingUsers = $this->staff_model->get('',['reports_to' =>$currentUserId],'');
        
        // Check if the current user is a Project Manager
        $isProjectManager = ($staff->todolist_pm == 1) && !empty($reportingUsers);
        $istoplinemanager = ($staff->todolist_pm == 1) && ($staff->toplinemanager == 1);

        if($isProjectManager && $currentUserId == $staffId){
            // Collect all staff IDs for the query, including the current user
            $staffIds = array_map(function ($user) {
                return $user['staffid'];
            }, $reportingUsers);
    
            // Ensure the current staff ID is included
            if (!in_array($staffId, $staffIds)) {
                $staffIds[] = $staffId;
            }
    
            // Get tasks and todos for all relevant staff IDs
            $allTasks = $this->get_tasks_by_multiple_staff_ids($staffIds);
            $allhuddlerTodos = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id($staffIds);
        } else {
            // If not a Project Manager, get tasks and todos only for the current staff ID
            $allTasks = $this->tasks_model->get_tasks_by_staff_id($staffId);
            $allhuddlerTodos = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id($staffId);
        }
    
        // Update and organize todos
        $allhuddlerTodos = $this->updateHuddlerTodos($allTasks, $allhuddlerTodos);
        $organizedData = $this->organizeHuddlerTodo($allhuddlerTodos);

        $huddlerTaskIds = array_column($allhuddlerTodos, 'task_id');
        $filteredTasks = array_filter($allTasks, fn($task) => !in_array($task['id'], $huddlerTaskIds));
        
        $organizedTasks = $this->organizeTasksByDate($filteredTasks);
        // Merge organized data and additional flags
        $data = array_merge($data, $organizedTasks, $organizedData, [
            'isProjectManager' => $isProjectManager,
            'istoplinemanager' => $istoplinemanager,
            'reportingUsers'     => ($istoplinemanager || $isProjectManager) ? $reportingUsers : []
        ]);
    
        // Get quantitative report if available
        if ($quantitativeReport = $this->GetStaffTaskPoints($staffId)) {
            $data['QuantitativeReport'] = $quantitativeReport;
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => 'success', 'data' => $data]);
            exit();
        }
        // Pass the data to the view
        $this->load->view('myteamtodo_edit', $data);
    }
    private function organizeHuddlerTodo($allhuddlerTodos){
        $currentUserId = $this->session->userdata('staff_user_id');
        $organizeData = [];

        $overdueTask = [];
        $todaysToDo = [];
        $needsAttention = [];
        $allHelpRequested = [];
        $toApprove = [];
        $today = strtotime(date('Y-m-d'));

        // Organize Huddler Todos into different sections
        foreach ($allhuddlerTodos as $Todo) {
            $dueDate = isset($Todo['due_date']) ? strtotime($Todo['due_date']) : null;
            $taskid = isset($Todo['task_id']) ? $Todo['task_id'] : null;
        
            if ($dueDate) {
                if ($dueDate === $today || $dueDate > $today) {
                    if ($Todo['status'] != 4 && $Todo['status'] != 7) {
                        $todaysToDo[] = $Todo;
                    }
                } elseif ($dueDate < $today) {
                    if ($Todo['status'] != 7 && $Todo['status'] != 4) {
                        $overdueTask[] = $Todo;
                    }
                }
            }

            if ($Todo['status'] == 3) {
                // if($Todo['staff_id']!== $currentUserId ){
                //     $toApprove[] = $Todo;
                // }
                $toApprove[] = $Todo;
            }

            if ($Todo['status'] == 7) {
                $needsAttention[] = $Todo;
            }

            if($Todo['status'] == 6) {
                $allHelpRequested[] = $Todo;
            }
        }
        // Sort tasks by due date in ascending order
        usort($overdueTask, function ($a, $b) {
            return strtotime($a['due_date']) - strtotime($b['due_date']);
        });
        usort($todaysToDo, function ($a, $b) {
            return strtotime($a['due_date']) - strtotime($b['due_date']);
        });
        usort($needsAttention, function ($a, $b) {
            return strtotime($a['due_date']) - strtotime($b['due_date']);
        });
        usort($toApprove, function ($a, $b) {
            return strtotime($a['due_date']) - strtotime($b['due_date']);
        });
        if (is_array($allHelpRequested) && !empty($allHelpRequested)) {
            usort($allHelpRequested, function ($a, $b) {
                return strtotime($a['helpreq_at']) - strtotime($b['helpreq_at']);
            });
        }
        $organizeData['overdueTask'] = $overdueTask;
        $organizeData['todaysToDo'] = $todaysToDo;
        $organizeData['needsAttention'] = $needsAttention;
        $organizeData['helpRequests'] = $allHelpRequested;
        $organizeData['toApprove'] = $toApprove;
        return $organizeData;
    }
    private function organizeTasksByDate(array $tasks): array
    {
        // Fetch all projects and map them by ID
        $projects = $this->db->select('id, name')->from('tblprojects')->get()->result_array();
        $projectsById = array_column($projects, 'name', 'id');

        // Prepare enriched tasks with project name, task points, and internal end date
        $tasksById = [];
        foreach ($tasks as $task) {
            $task['project_name'] = $projectsById[$task['rel_id']] ?? 'Unknown Project';
            $task['task_point'] = $this->getTaskPoint($task['id']);
            $task['internal_end'] = $this->getInternalEndDate($task['id']);
            $tasksById[$task['id']] = $task; // Store task by ID for quick lookup
        }

        // Initialize arrays for organized tasks by date
        $organizedTasks = [
            'dueToday'     => [],
            'dueNext5Days' => [],
            'dueNext25Days'=> [],
        ];

        // Define time ranges
        $today = strtotime(date('Y-m-d'));
        $next5Days = strtotime('+5 days', $today);
        $next25Days = strtotime('+25 days', $today);

        // Loop through tasks and classify them by date
        foreach ($tasksById as $task) {
            $startDate = isset($task['startdate']) ? strtotime($task['startdate']) : null;

            // If start date exists, classify tasks into relevant sections
            if ($startDate && $task['status'] != 5 && $task['status'] != 2) {
                if ($startDate <= $today) {
                    $organizedTasks['dueToday'][] = $task;
                } elseif ($startDate > $today && $startDate <= $next5Days) {
                    $organizedTasks['dueNext5Days'][] = $task;
                } elseif ($startDate > $next5Days) {
                    $organizedTasks['dueNext25Days'][] = $task;
                }
            }
        }

        return $organizedTasks;
    }


    /**
     * Update Huddler todos with relevant project data and status changes.
     *
     * @param array $tasks - List of all tasks assigned to the staff.
     * @param array $todos - List of Huddler todos for the staff.
     * @return array - Updated list of Huddler todos.
     */
    private function updateHuddlerTodos(array $tasks, array $todos): array
    {
        // Fetch all projects and map them by ID
        $projects = $this->db->select('id, name')->from('tblprojects')->get()->result_array();
        $projectsById = array_column($projects, 'name', 'id');

        // Prepare enriched tasks with project name, points, internal end date, and members
        $tasksById = [];
        foreach ($tasks as $task) {
            $task['project_name'] = $projectsById[$task['rel_id']] ?? null;
            $task['task_point'] = $this->getTaskPoint($task['id']);
            $task['internal_end'] = $this->getInternalEndDate($task['id']);

            // Fetch task members
            $members = $this->tasks_model->get_staff_members_that_can_access_task($task['id']);
            $task['task_members'] = array_map(function ($member) {
                return [
                    'id' => $member['staffid'],
                    'name' => $member['firstname'] . ' ' . $member['lastname']
                ];
            }, $members);

            // Store enriched task by ID for quick lookup
            $tasksById[$task['id']] = $task;
        }

        // Update todos with task data and handle status changes
        foreach ($todos as &$todo) {
            $task = $tasksById[$todo['task_id']] ?? null;
            $imgTag = staff_profile_image(
                $todo['staff_id'],
                [
                    'img', 'img-responsive', 'staff-profile-image-small',
                    'tw-h-5', 'tw-w-5', 'tw-inline-block', 'tw-rounded-full',
                    'tw-ring-2', 'tw-ring-white'
                ],
                'small',
                ['data-toggle' => 'tooltip', 'data-title' => get_staff_full_name($todo['staff_id'])]
            );
            if ($task) {
                $todo = array_merge($todo, [
                    'project_name' => $task['project_name'],
                    'task_point'   => $task['task_point'],
                    'internal_end' => $task['internal_end'],
                    'rel_id'       => $task['rel_id'] ?? null,
                    'task_members' => $task['task_members'], // Assign task members to the todo
                    'img_tag'      => $imgTag
                ]);

                error_log("Task Status: {$task['status']}, Todo Status: {$todo['status']}");

                // Check for client comments to update the status
                $clientComments = $this->tasks_model->get_task_comments($task['id']);
                if (!empty($clientComments) && isset($clientComments[0]['contact_id']) && $clientComments[0]['contact_id'] != 0) {
                    $this->updatetaskstatus($todo['id'], ['new_comment_by_client' => 1]);
                }

                // Update the todo status based on task status and client comments
                if ($task['status'] == 2 || ($task['status'] == 5 && !empty($todo['new_comment_by_client']))) {
                    $todo['status'] = 7;
                    error_log("Todo status updated to 7 for Task ID: {$task['id']}");
                }
            }
        }

        return $todos;
    }

    // Private method to get tasks by IDs
    private function get_tasks_by_ids($ids, $where = [])
    {
        if (empty($ids)) {
            return [];
        }

        $this->db->where($where);
        $this->db->where_in('id', $ids);

        return $this->db->get(db_prefix() . 'tasks')->result_array();
    }
    private function get_tasks_by_multiple_staff_ids($staffIds, $where = [], $isToplineManager = false)
    {
        // Ensure $staffIds is an array and not empty
        if (!is_array($staffIds) || empty($staffIds)) {
            throw new InvalidArgumentException('Invalid or empty staff IDs array provided.');
        }

        // Escape each staff ID to prevent SQL injection
        $escapedIds = array_map([$this->db, 'escape_str'], $staffIds);
        $escapedIdsString = implode(',', $escapedIds);

        // Apply additional where conditions if any
        if (!empty($where)) {
            $this->db->where($where);
        }

        // Use the IN clause to handle multiple staff IDs
        //$this->db->where('id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid IN (' . $escapedIdsString . '))');
        if ($isToplineManager) {
            // If user is a Topline Manager, get tasks from assigned & unassigned projects
            $this->db->where("
                id IN (SELECT taskid FROM " . db_prefix() . "task_assigned WHERE staffid IN ($escapedIdsString))
                OR rel_id IN (SELECT project_id FROM " . db_prefix() . "project_members WHERE staff_id IN ($escapedIdsString))
                OR rel_id IS NULL
            ");
        } else {
            // Regular staff - Get only assigned tasks
            $this->db->where("id IN (SELECT taskid FROM " . db_prefix() . "task_assigned WHERE staffid IN ($escapedIdsString))");
        }

        // Fetch and return the result set as an array
        return $this->db->get(db_prefix() . 'tasks')->result_array();
    }
    
    public function GetTaskbyIds()
    {
        $data = [];
        
        if ($this->input->is_ajax_request()) {
            $ids = $this->input->post('task_ids');
            $pmId = $this->input->post('pmId');
            if(isset($pmId) && $pmId!= ""){
                $staffId = $pmId;
            }else{
                $staffId = $this->session->userdata('staff_user_id');
            }
            // Ensure $ids is an array
            if (is_array($ids)) {
                // Get tasks by multiple IDs
                $data['tasks'] = $this->get_tasks_by_ids($ids);
            } else {
                // Get task by ID
                $taskObjects = [$this->tasks_model->get($ids)];
                $data['tasks'] = array_map(function($task) {
                    return (array) $task;
                }, $taskObjects);
            }
            // Fetch all projects where id matches task rel_id and rel_type is 'project'
            $this->db->select('id, name');
            $this->db->from('tblprojects');
            $projects = $this->db->get()->result_array();
    
            // Map project names by ID
            $projectsById = [];
            foreach ($projects as $project) {
                $projectsById[$project['id']] = $project['name'];
            }
        
            // Add project_name and rel_id to each task based on rel_id and rel_type
            foreach ($data['tasks'] as &$task) {
                if(isset($task['rel_id']) && isset($task['rel_type'])){
                    if ($task['rel_type'] == 'project' && isset($projectsById[$task['rel_id']])) {
                        $task['project_name'] = $projectsById[$task['rel_id']];
                    } else {
                        $task['project_name'] = null;
                    }
                }
                $task['img_tag'] = staff_profile_image($staffId, ['img', 'img-responsive', 'staff-profile-image-small', 'tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white'],'small', ['data-toggle' => 'tooltip','data-title'  => get_staff_full_name($staffId),]);
            }
    
            // Respond with the tasks
            echo json_encode(['status' => 'success', 'tasks' => $data['tasks']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Not a valid request']);
        }
    }
    
    public function create_todays_plan()
    {
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['status' => 'error', 'message' => 'Not a valid request']);
            return;
        }
    
        $ids            = $this->input->post('task_ids');
        $removeTaskIds  = $this->input->post('remove_task_ids');
        $pmId           = $this->input->post('pmId');
        $isHuddlerSODSubmited = $this->input->post('huddlerSOD');
        
        // Ensure $ids is an array
        if (!is_array($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Assign one task atleast from Starts Today']);
            return;
        }
    
        $staffId              = $pmId ?: $this->session->userdata('staff_user_id');
        $createdBy            = $pmId ? 'PM/TL' : 'self';
        $employee             = $this->staff_model->get($staffId, ['active' => 1]);
        $notificationMessage  = $pmId ? "Today's Plan Saved of {$employee->firstname}" : "Today's plan and attendance is marked";
        $taskIds              = implode(',', $ids);
        $actionType           = ($createdBy === 'self') ? 'assign_self' : 'assign_by_manager';
        $notificationErrormsg = 'No new tasks assigned or deleted.';
    
        // Handle task removal
        if (is_array($removeTaskIds) && !empty($removeTaskIds)) {
            $deletedTasks = $this->HuddlerTodosModel->delete_tasks($staffId, $removeTaskIds);
            if (!$deletedTasks) {
                $notificationMessage = "The selected tasks Deleted successfully";
                $notificationErrormsg = $notificationMessage;
            }
        }
    
        // Get tasks by IDs
        $tasks           = $this->get_tasks_by_ids($ids);
        $existingTaskIds = $this->HuddlerTodosModel->get_existing_task_ids($staffId, $ids);
        $existingTaskIds = array_column($existingTaskIds, 'task_id');
        $isModified      = $this->HuddlerTodosModel->has_todays_todo_submitted($staffId);
    
        // Prepare new tasks for insertion
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
    
        // Insert new tasks into huddler_todos table | Log actions and notify users
        if (!empty($insertData)) {
            $logData          = $this->prepareLogData($staffId, $createdBy, $removeTaskIds);
            $logActionComment = ($createdBy === 'self') ? $this->generateLogActionComment('self_assign', $logData) : $this->generateLogActionComment('plan_edited', $logData);
    
            $this->HuddlerTodosModel->insertMultiple($insertData);
            $this->session->set_userdata('todo_submitted', date('Y-m-d'));
            $this->logAction($actionType, $logActionComment, '', $taskIds, $staffId, get_staff_full_name($staffId), 2, $insertData);
            if($isHuddlerSODSubmited){
                log_activity('User Successfully Submitted SOD for ' . date('Y-m-d'), $staffId);
            }
            $this->SendSODEmails($staffId, $isModified, $createdBy);
            
            echo json_encode(['status' => 'success', 'message' => $notificationMessage]);
        } else {
            echo json_encode(['status' => 'warning', 'message' =>  $notificationErrormsg]);
        }
    }
    
    private function SendSODEmails($staffId, $isModified, $createdBy){
    
        // Prepare email data for SOD emails
        $allTasks               = $this->tasks_model->get_tasks_by_staff_id($staffId);
        $allhuddlerTodos        = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id($staffId);
        $updatedHuddlerTodos    = $this->updateHuddlerTodos($allTasks, $allhuddlerTodos);
        $organizedData           = $this->organizeHuddlerTodo($updatedHuddlerTodos);
        $employee               = $this->staff_model->get($staffId, ['active' => 1]);
        $manager                = $this->staff_model->get($employee->reports_to, ['active' => 1]);
        if (!$manager) {
            log_message('error', "Manager not found for employee ID: {$staffId}");
            set_alert('danger', "Manager not found for employee ID: {$staffId}");
            return;
        }
    
        $tasksForEmail = [];
        $notifiedUsers = [];
        $statusLabels = [
            'overdueTask' => 'Overdue',
            'todaysToDo' => "Today’s",
            'helpRequests' => 'HR Pending',
            'toApprove' => 'Approval Pending',
            'needsAttention' => 'Needs Attention'
        ];
    
        foreach ($organizedData as $status => $taskArray) {
            foreach ($taskArray as $task) {
                $task['status'] = $statusLabels[$status] ?? 'Unknown';
                $tasksForEmail[$task['task_id']] = $this->formatTaskForEmail($task);
            }
        }
        ksort($tasksForEmail);
        // Convert back to a numeric array
        $tasksForEmail = array_values($tasksForEmail);
    
        if($createdBy === 'self') {
                           
            if($isModified){
                $descriptionSODForEmp   = "Your SOD report is modified.";
                $descriptionSODForPm    = $employee->firstname." modified the SOD Report.";
                $EmailSubjectSODForEmp  = "You Modified your work plan on - ".date('M jS, Y, \a\t h:i:s A T');
                $EmailSubjectSODForPm   = $employee->firstname."(".$staffId.") modified their work plan on - ".date('M jS, Y, \a\t h:i:s A T');
                $EmailTemplateSODForEmp = $this->createModifiedWorkPlanEmailForEmployee($employee->firstname, date('M jS, Y, \a\t h:i:s A T'), $tasksForEmail);
                $EmailTemplateSODForPm  = $this->createModifiedWorkPlanEmailForManager($manager->firstname, $employee->firstname, $staffId, date('M jS, Y, \a\t h:i:s A T'), $tasksForEmail);
           }else{
                $descriptionSODForEmp   = "Your SOD report is submitted";
                $descriptionSODForPm    = $employee->firstname." submitted the SOD Report";
                $EmailSubjectSODForEmp  = "Your start of day report is submitted - ".date('M jS, Y');
                $EmailSubjectSODForPm   = "Start of day report is submitted by ".$employee->firstname." (".$staffId.") - ".date('M jS, Y');
                $EmailTemplateSODForEmp = $this->createSODEmailForEmployee($employee->firstname, date('M jS, Y, \a\t h:i:s A T'), $tasksForEmail);
                $EmailTemplateSODForPm  = $this->createSODEmailForManager($manager->firstname, $employee->firstname, $staffId, date('M jS, Y, \a\t h:i:s A T'), $tasksForEmail);
           }
           $this->emails_model->send_simple_email($employee->email, $EmailSubjectSODForEmp, $EmailTemplateSODForEmp);
           $this->emails_model->send_simple_email($manager->email, $EmailSubjectSODForPm, $EmailTemplateSODForPm);
           $notified = add_notification(['description' => $descriptionSODForEmp, 'touserid' => $staffId, 'link' => 'todo/mytodos']);
           $notified = add_notification(['description' => $descriptionSODForPm, 'touserid' => $manager->staffid, 'link' => 'todo/myteamtodos']);
        } else {
                $descriptionSODForEmp   = "Your Manager modified your SOD report";
                $descriptionSODForPm    = "SOD Report of ".$employee->firstname." is modified";
                $EmailSubjectSODForEmp  = "Your Manager modified your work plan on - ".date('M jS, Y, \a\t h:i:s A T');
                $EmailSubjectSODForPm   = "You Modified Today’s work plan of".$employee->firstname." (".$staffId.") on - ".date('M jS, Y, \a\t h:i:s A T');
                $EmailTemplateSODForEmp = $this->createModifiedWorkPlanForEmployeeEmail($manager->firstname, $employee->firstname, date('M jS, Y, \a\t h:i:s A T'), $tasksForEmail);
                $EmailTemplateSODForPm  = $this->createModifiedWorkPlanByManagerEmail($manager->firstname, $employee->firstname, $staffId, date('M jS, Y, \a\t h:i:s A T'), $tasksForEmail);
    
                $this->emails_model->send_simple_email($employee->email, $EmailSubjectSODForEmp, $EmailTemplateSODForEmp);
                $this->emails_model->send_simple_email($manager->email, $EmailSubjectSODForPm, $EmailTemplateSODForPm);
                $notified = add_notification(['description' => $descriptionSODForEmp, 'touserid' => $staffId, 'link' => 'todo/mytodos']);
                $notified = add_notification(['description' => $descriptionSODForPm, 'touserid' => $manager->staffid, 'link' => 'todo/myteamtodos']);
        }
        if ($notified) {
            array_push($notifiedUsers, $manager->staffid, $employee->staffid);
        }
        pusher_trigger_notification($notifiedUsers);

    }
    
    private function prepareLogData($staffId, $createdBy, $removeTaskIds)
    {
        $allhuddlerTodos        = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id($staffId);
        $allTasks               = $this->tasks_model->get_tasks_by_staff_id($staffId);
        $updatedHuddlerTodos    = $this->updateHuddlerTodos($allTasks, $allhuddlerTodos);
        $organizeData           = $this->organizeHuddlerTodo($updatedHuddlerTodos);
        $employee               = $this->staff_model->get($staffId, ['active' => 1]);
        $manager                = $this->staff_model->get($employee->reports_to, ['active' => 1]);
        $submissionTime         = date("M jS, Y \a\\t h:i:s A T");
    
        return $createdBy === 'self' ? [
                'employee_name'           => $employee->firstname,
                'employee_id'             => $staffId,
                'submission_time'         => $submissionTime,
                'today_tasks'             => array_column($organizeData['todaysToDo'], 'task_id'),
                'overdue_tasks'           => array_column($organizeData['overdueTask'], 'task_id'),
                'approval_due_tasks'      => array_column($organizeData['toApprove'], 'task_id'),
                'hr_due_tasks'            => array_column($organizeData['helpRequests'], 'task_id'),
                'tasks_needs_attention'   => array_column($organizeData['needsAttention'], 'task_id'),
                'adhoc_tasks'             => [],
                'tickets_needs_attention' => []
        ] : [
                'employee_name'           => $employee->firstname,
                'employee_id'             => $staffId,
                'plan_date'               => $submissionTime,
                'editor_name'             => $employee->firstname,
                'editor_id'               => $staffId,
                'previous_id'             => $removeTaskIds ?? [],
                'edit_time'               => $submissionTime,
                'today_task_ids'          => array_column($organizeData['todaysToDo'], 'task_id'),
                'overdue_task_ids'        => array_column($organizeData['overdueTask'], 'task_id'),
                'approval_due_tasks'      => array_column($organizeData['toApprove'], 'task_id'),
                'hr_due'                  => array_column($organizeData['helpRequests'], 'task_id'),
                'tasks_needs_attention'   => array_column($organizeData['needsAttention'], 'task_id'),
                'adhoc_tasks'             => [],
                'tickets_needs_attention' => []
        ];
    }
    public function exportcsv() {
        $filename = 'report.csv';
    
        $this->load->dbutil();
        $staffList = $this->db->select('staffid, reports_to AS managerid')
                              ->from('tblstaff')
                              ->where('active = 1 AND role = 1')
                              ->get()
                              ->result_array();
    
        $data  = [];  
        $year  = date('Y');
        $month = date('M');
    
        // Loop through each staff member
        foreach ($staffList as $row) {
            $quantitativeReport = $this->GetStaffTaskPoints($row['staffid']);
            $qualitativeReport  = $this->GetStaffQualitativeReport($row['staffid']);
    
            $data[] = [
                'year' => $year,
                'month' => $month,
                'manager_name' => get_staff_full_name($row['managerid']),
                'staffId' => $row['staffid'],
                'staffName' => get_staff_full_name($row['staffid']),
                'ontimecount' => $qualitativeReport['On_Time']['ratio'],
                'ontimepct' => $qualitativeReport['On_Time']['percentage'],
                'reasonabledelaycount' => $qualitativeReport['Reasonable_Delay']['ratio'],
                'reasonabledelaypct' => $qualitativeReport['Reasonable_Delay']['percentage'],
                'delaycount' => $qualitativeReport['Delay']['ratio'],
                'delaypct' => $qualitativeReport['Delay']['percentage'],
                'pointearned' => $quantitativeReport['Earned_This_Year'],
                'pointdue' => $quantitativeReport['Points_Due']
            ];
        }
    
        // File creation
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
    
        $output = fopen('php://output', 'w');
    
        // Header row
        fputcsv($output, [
            'Year', 'Month', 'Manager Name', 'Staff Id', 'Staff Name',
            'On Time Count', 'On Time Pct', 'Reasonable Delay Count', 
            'Reasonable Delay Pct', 'Delay Count', 'Delay Pct', 
            'Point Earned', 'Point Due'
        ]);
    
        // Data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    
        fclose($output);
        exit;
    }
    
    public function getQuestions()
    {
        if ($this->input->is_ajax_request()) {
            $ids = $this->input->post('task_ids');
            $category = $this->input->post('category');
            $status = $this->input->post('status');

            // Get tasks by ID
            $tasks = $this->tasks_model->get($ids);
            $questions = $this->QuestionsModel->get_by_category_and_status($category, $status);

            $questionAnswer = [];

            if ($category === 'On Approve') {
                $answers = $this->HuddlerTodosModel->get_answers($ids, 3);
                $questionsData = json_decode($answers['questions_data'], true);

                foreach ($questionsData as $questionId => $answer) {
                    if (strpos($questionId, 'question_') === false) {
                        continue;
                    }
                    $numericId = str_replace('question_', '', $questionId);
                    // Get the corresponding question text from QuestionsModel
                    $questionText = $this->QuestionsModel->get_by_id($numericId);
                    //echo $this->db->last_query().'/n';

                    if ($questionText) {
                        $questionAnswer[] = [
                            'id' => $numericId,
                            'options' => $questionText['options'],
                            'question_text' => $questionText['question_text'],
                            'answer' => $answer
                        ];
                    }
                }
            }

            // Respond with the tasks and the questionAnswer JSON
            echo json_encode([
                'status' => 'success',
                'tasks' => $tasks,
                'questions' => $questions,
                'questionAnswer' => $questionAnswer
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Not a valid request']);
        }
    }
    public function submitQuestions()
    {
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            return;
        }
    
        $data = $this->input->post();
        $questionsData = json_encode($data);
        $updateData = [
            'questions_data' => $questionsData,
            'date_added' => date('Y-m-d H:i:s')
        ];
        $submited_by = $this->db->select('staff_id')
                                ->where('id', $data['todoid'])
                                ->get('tblhuddlertodos')
                                ->row();
        $submited_by = $submited_by->staff_id;
        $employee = $this->staff_model->get($submited_by, ['active' => 1]);
        $taskStatus = $taskMessage = $status = $logAction_actionType = $logAction_comment = null;
        $taskComments = $this->tasks_model->get_task_comments($data['taskid']);
        $latestComment = !empty($taskComments) ? $taskComments[0] : null;
        // $response_content = $latestComment ? "{$latestComment['dateadded']}\n{$latestComment['staff_full_name']}\n{$latestComment['content']}" : "No comments found.";
        $response_content = $latestComment ? "\n{$latestComment['content']}" : "No comments found.";
        $managerId = '';
        if ($employee) {
            $manager = $this->staff_model->get($employee->reports_to, ['active' => 1]);
            if ($manager) {
                $managerId = $manager->staffid;
                // Prepare the data array for the 'task_submitted' case
                $logData = [
                    'employee_name' => $employee->firstname,
                    'employee_id' => $employee->staffid,
                    'manager_name' => $manager->firstname,
                    'manager_id' => $manager->staffid,
                    'task_id' => !empty($data['taskid']) ? $data['taskid'] : 'on fly task',
                    'todo_id' => $data['todoid'],
                    'ticket_id' => !empty($data['ticketid']) ? $data['ticketid'] : '', 
                    'comment' => $response_content,
                ];
            } else {
                $logAction_comment = "Manager not found for employee id: {$employee->reports_to}\n";
            }
        } else {
            $logAction_comment = "Employee not found for submited_by id: {$data['submited_by']}\n";
        }

    
        // Handle category-specific status updates
        switch ($data['category']) {
            case 'On submission':
                $updateData['status']   = 3; // Completed by employee
                $taskStatus             = 3; // Testing
                $taskMessage            = 'Task set to Testing. To Do sent for Approval.';
                $status                 = 'success';
                $logAction_actionType   = 'on_submission';
                $logActionComment       = (isset($logData) && $logData) ? $this->generateLogActionComment('task_submitted', $logData) : $logAction_comment;
                break;
    
            case 'On Approve':
                if ($data['question_10'] === 'Yes' && $data['question_12'] === 'No') {
                    $updateData['status']   = 4; // PM Approved
                    $updateData['new_comment_by_client']   = 0;
                    $updateData['needs_attention_status ']  = null;
                    $taskStatus             = 2; // Awaiting Feedback
                    $taskMessage            = 'To Do Approved. Task set to Awaiting Feedback.';
                    $status                 = 'success';
                    $logAction_actionType   = 'on_approved';
                    //$logAction_comment      = 'Task ID: ' . $data['taskid'] . ' Approved by PM and marked as awaiting feedback.';
                    $logActionComment       = (isset($logData) && $logData) ? $this->generateLogActionComment('todo_approved', $logData) : $logAction_comment;
                } elseif ($data['question_10'] === 'Yes' && $data['question_12'] === 'Yes') {
                    $updateData['status']   = 5; // Re-Assign
                    $taskStatus             = 3; // Testing
                    $updateData['new_comment_by_client']   = 0;
                    $taskMessage            = 'To Do of '.$employee->firstname.' rejected.';
                    $status                 = 'warning';
                    $logAction_actionType   = 'on_rejected';
                    //$logAction_comment      = 'Task ID: ' . $data['taskid'] . ' Rejected by PM and marked as Testing.';
                    $logActionComment       = (isset($logData) && $logData) ? $this->generateLogActionComment('todo_rejected', $logData) : $logAction_comment;
                }
                break;
    
            case 'HR submission':
                $updateData['status'] = ($data['question_11'] === 'Yes') ? 2 : 2; // Help Requested/No Help
                $this->HelpRequestModel->update_request(['status' => $updateData['status']], $data['hrId']);
                $taskMessage            = 'Help Requested Successfully!';
                $status                 = 'success';
                $logAction_actionType   = 'help_provided';
                //$logAction_comment      = 'Task ID: ' . $data['taskid'] . ' Help Request marked as help provided by PM.';
                break;
    
            case 'Mark as Complete':
                $updateData['status']   = 4; // complete
                $updateData['new_comment_by_client']   = 0; 
                $taskStatus             = 5; // Complete
                $taskMessage            = 'Completed.ToDo & Task set to Completed.';
                $status                 = 'success';
                $logAction_actionType   = 'Need_attention_mark_as_complete';
                //$logAction_comment      = 'Task ID: ' . $data['taskid'] . ' Completed, ToDo & Respective Task Mark as Completed.';
                $logActionComment       = (isset($logData) && $logData) ? $this->generateLogActionComment('mark_as_completed', $logData) : $logAction_comment;
                unset($updateData['questions_data']);
                break;

            case 'Bug Found':
                $updateData['status']   = 5; // Re-Assign
                $updateData['new_comment_by_client']   = 0; 
                $taskStatus             = 3; // Testing
                $updateData['needs_attention_status ']  = 'Bug Found';
                $taskMessage            = 'Bug Found. ToDo Re-Assigned. Task set to Testing.';
                $status                 = 'success';
                $logAction_actionType   = 'need_attention_bug';
                //$logAction_comment      = 'Task ID: ' . $data['taskid'] . ' Bug Found, To Do Re-Assigned. Task marked as Testing';
                $logActionComment       = (isset($logData) && $logData) ? $this->generateLogActionComment('bug_found', $logData) : $logAction_comment;
                
                // Assign This Task/Todo to Differnet Employee
                if(isset($data['assignTo']) && !empty($data['assignTo'])){
               
                    $currentStaffid = $this->HuddlerTodosModel->get_staffid_by_taskid($data['taskid']);
                    $currentStaffid = $currentStaffid['staff_id'];
                    if (array_key_exists($data['assignTo'], $data['assignees_ids'])) {
                        echo json_encode(['status' => 'success', 'taskStatus' =>  'success', 'message' => 'This staff member is already assigned to this task.']);
                        exit;
                    }else{

                        $this->tasks_model->add_task_assignees(['taskid' => $data['taskid'], 'assignee' => $data['assignTo']]);
                        $this->HuddlerTodosModel->update(['staff_id' => $data['assignTo']], $data['todoid']);
                    }
                    if (!empty($currentStaffid) && !empty($data['assignees_ids'])) {
                        foreach ($data['assignees_ids'] as $staffId => $assigneeId) {  
                            $taskid = $data['taskid'];
                            
                            if ($staffId == $currentStaffid && !empty($assigneeId) && !empty($taskid)) {
                                $this->tasks_model->remove_assignee($assigneeId, $taskid);
                            }
                        }
                    }
                    
                
                $updateData['status'] = 2; // Assigned
                    
                }
                unset($updateData['questions_data']);
                break;
    
            case 'New Work':
                $updateData['status']   = 4; // Sent to PM
                $updateData['needs_attention_status ']  = 'New Work';
                $updateData['new_comment_by_client']   = 0; 
                $taskMessage            = 'New Work. Client & Sales Team are informed.';
                $status                 = 'success';
                $logAction_actionType   = 'need_attention_new_work';
                //$logAction_comment      = 'Task ID: ' . $data['taskid'] . ' New Work, client and Sales Teams both are informed.';
                $logActionComment       = (isset($logData) && $logData) ? $this->generateLogActionComment('new_work', $logData) : $logAction_comment;
                break;

            case 'No Problem':
                $updateData['status']                   = 4; // complete
                $updateData['new_comment_by_client']    = 0; 
                $taskStatus                             = 5; // Complete
                $updateData['needs_attention_status ']  = 'No Problem';
                $taskMessage                            = 'No Problem. ToDo & Task set to Completed.';
                $status                                 = 'success';
                $logAction_actionType                   = 'need_attention_No_problem';
                //$logAction_comment                    = 'Task ID: ' . $data['taskid'] . ' No Problem, ToDo & Respective Task Mark as Completed.';
                $logActionComment                       = (isset($logData) && $logData) ? $this->generateLogActionComment('no_problem', $logData) : $logAction_comment;
                unset($updateData['questions_data']);
                break;
        }
    
        // Update task status if defined
        if ($taskStatus !== null) {
            $this->tasks_model->mark_as($taskStatus, $data['taskid']);
        }
    
        // Update the huddlertodos status
        $this->updatetaskstatus($data['todoid'], $updateData);
    
        // Log the action
        $this->logAction($logAction_actionType, $logActionComment, $data['todoid'], $data['taskid'], $data['submited_by'],get_staff_full_name($data['submited_by']), $updateData['status'], $questionsData);
        $emailmess = '';
        // Notify staff of rejection if status is 5 (Re-Assign)
        if ($updateData['status'] === 5) {
          $this->notifyStaffOfRejection($data['taskid'], $submited_by, $questionsData);
          $this->updateNegativePoints($data['taskid'], $data['todoid'], $submited_by, $managerId);
        }
        echo json_encode(['status' => 'success', 'taskStatus' =>  $status, 'message' => $taskMessage]);
    }
    public function createHelpRequest()
    {
        $data = $this->input->post();
        $updateData = [];
        $todoID = $data['todoid'];
    
        $updateData['status'] = 6;
        $updateData['helpreq_remarks'] = $data['emp_remarks'];
        $updateData['video_link'] = $data['video_link'];
        $updateData['helpreq_at'] = (new DateTime())->format('Y-m-d H:i:s'); 
    
        // Check if a help request already exists for the given todoID
        $todoCurrentStatus = $this->HuddlerTodosModel->get_status_by_id($todoID);
        $additonalData = json_encode($updateData);
       
        if ($todoCurrentStatus && $todoCurrentStatus == 6) {
            set_alert('warning', 'A help request already exists for this task.');
        } else {
            // Create a new help request by updating the task
            $input = $this->HuddlerTodosModel->update($updateData, $todoID);
            // Get employee details
            $employee = $this->staff_model->get($data['requested_by'], ['active' => 1]);

            if ($employee) {
                // Get manager details if employee exists
                $manager = $this->staff_model->get($employee->reports_to, ['active' => 1]);

                // Prepare the data array for the 'help_request_submitted' case
                $logData = [
                    'employee_name' => $employee->firstname,
                    'employee_id' => $employee->staffid,
                    'manager_name' => $manager ? $manager->firstname : 'No Manager',
                    'manager_id' => $manager ? $manager->staffid : 'N/A',
                    'todo_id' => $data['todoid'],
                    'task_id' => !empty($data['taskid']) ? $data['taskid'] : 'on fly task', 
                    'ticket_id' => !empty($data['ticketid']) ? $data['ticketid'] : '', 
                ];
                $logActionComment = $this->generateLogActionComment('help_request_submitted', $logData);
            } else {
                echo "Employee not found for requested_by id: {$data['requested_by']}\n";
            }


    
            if ($input) {
                set_alert('success', 'Your help request sent to manager');
                // Log the action
                $this->logAction('help_requested', $logActionComment, $data['todoid'], $data['taskid'], $data['requested_by'],get_staff_full_name($data['requested_by']), $updateData['status'], $additonalData);
                $this->notifyManagerHelpRequested($data['taskid'], $data['todoid'],$data['requested_by'], $updateData);
               
            } else {
                set_alert('danger', 'Failed to create help request');
            }
        }
    
        $previousUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : admin_url('todo/mytodos');
        redirect($previousUrl);
    }
    
    public function getHelpRequestDetails()
    {
        if ($this->input->is_ajax_request()) {
            $todoId = $this->input->post('todoid');
            $taskId = $this->input->post('taskid');
            $staffId = $this->input->post('staffId');
    
            // Fetch the help request details
            // $helpRequest = $this->HelpRequestModel->get_request_by_todoid($todoId);
            $helpRequest = $this->HuddlerTodosModel->getHelpRequestDetails($todoId);
            $taskDetails = $this->tasks_model->get($taskId);
            $reportingUsers = $this->staff_model->get('',['reports_to' =>$staffId],'');
    
            if ($helpRequest && $taskDetails) {
                $response = [
                    'status' => 'success',
                    'tasks' => $taskDetails,
                    'helprequest' => $helpRequest,
                    'employees' => $reportingUsers
                ];
            } else {
                $response = ['status' => 'error', 'message' => 'Help request not found.'];
            }
    
            echo json_encode($response);
        }
    }
    public function render_select_add_edit_members()
    {
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            return;
        }
    
        $taskId = $this->input->post('taskid');
         $staffMembers = $this->staff_model->get('', ['active' => 1]); 
        // $taskMembers = $this->tasks_model->get_staff_members_that_can_access_task($taskId); 

        // $staffMembers = $this->tasks_model->get_staff_members_that_can_access_task($taskId); 
        $taskMembers = $this->tasks_model->get_task_assignees($taskId);
       
        // Prepare selected members for the select field
        $selected = array_column($taskMembers, 'assigneeid');
    
        // Generate HTML for the select element
        $selectHtml = render_select(
            'project_members[]', 
            $staffMembers, 
            ['staffid', ['firstname', 'lastname']], 
            'project_members', 
            $selected, 
            ['data-actions-box' => true], 
            [], '', '', false
        );
    
        echo json_encode(['status' => 'success', 'html' => $selectHtml]);
    }
    public function add_edit_members()
    {
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            return;
        }
        $currentUser = $this->session->userdata('staff_user_id');
        $currentUserName = get_staff_full_name($currentUser);
        $taskId = $this->input->post('taskid');
        $todoId = $this->input->post('todoid');
        $members = $this->input->post('project_members') ?? [];  // Ensure it's an array  // Selected members

        // Fetch current task members
        $currentMembers = $this->tasks_model->get_staff_members_that_can_access_task($taskId);
        $taskMembers = $this->tasks_model->get_task_assignees($taskId);
        $currentMemberIds = array_column($taskMembers, 'assigneeid');
        
        if (!is_array($members)) {
            $members = [];
        }
        // Calculate members to add and remove
        $membersToAdd = array_diff($members, $currentMemberIds);
        $membersToRemove = array_diff($currentMemberIds, $members);
       
        // Add new members
        foreach ($membersToAdd as $memberId) {
            $this->tasks_model->add_task_assignees(['taskid' => $taskId, 'assignee' => $memberId]);
            $this->HuddlerTodosModel->update(['staff_id' => $memberId], $todoId);
            // Log action for member addition
            $assigned_to_Name = get_staff_full_name($memberId);
            $this->logAction(
                'assignment_change',
                "Staff assignment changed by {$currentUserName} (ID: {$currentUser})."."\n"."
                To-Do and related task assigned to: {$assigned_to_Name} (ID: {$memberId})",
                $todoId,
                $taskId,
                $currentUser,
                $currentUserName,
                '',
                ['added_member_id' => $memberId]
            );
        }

        // Remove members
        foreach ($membersToRemove as $memberId) {
            foreach ($taskMembers as $taskAssignee) {
                if ($taskAssignee['assigneeid'] == $memberId) {
                    $this->tasks_model->remove_assignee($taskAssignee['id'], $taskId);
                    $removed_to_Name = get_staff_full_name($memberId);
                    // Log action for member removal
                    $this->logAction(
                        'assignment_change',
                        "Staff assignment changed by {$currentUserName} (ID: {$currentUser})"."\n"." 
                        Assignment removed from staff ID: {$removed_to_Name} (ID: {$memberId})",
                        $todoId,
                        $taskId,
                        $currentUser,
                        $currentUserName,
                        '',
                        ['removed_member_id' => $memberId]
                    );
                    break;
                }
            }
            //$this->HuddlerTodosModel->update(['staff_id' => $memberId], $todoId);
        }

        echo json_encode(['status' => 'success', 'message' => 'Members updated successfully']);
        exit();
    }

    public function UpdateHelpRequest() {
        
            $data = $this->input->post();
            $id = $data['id'];
            // Prepare the data array with only the fields that are being updated
            $help_request_data = [];
            
            if (isset($data['assignTo']) && $data['assignTo'] != '') {
                $help_request_data['staff_id'] = $data['assignTo'];
                $help_request_data['status'] = 2;
            }
    
            if (isset($data['pm_remarks'])) {
                $help_request_data['pm_remarks'] = $data['pm_remarks'];
            }
            // Update the help request only if there is data to update
            if (!empty($help_request_data)) {
                if($data['assignTo'] == $data['staffId'] || $data['assignTo'] == $data['createdby']){
                    $notificationMessage = ' Help request solved';
                    $help_request_data['helpprovided_at'] = (new DateTime())->format('Y-m-d H:i:s'); 
                    $this->notifyEmployeeHelpRequestClosed($data['taskid'], $data['id'], $data['assignTo'], $data['staffId'], $data['pm_remarks']);
                    $employee = $this->staff_model->get($data['createdby'], ['active' => 1]);
                    if ($employee) {
                        $manager = $this->staff_model->get($employee->reports_to, ['active' => 1]);
                        if ($manager) {
                            $logData = [
                                'employee_name' => $employee->firstname,
                                'employee_id' => $employee->staffid,
                                'manager_name' => $manager->firstname,
                                'manager_id' => $manager->staffid,
                                'todo_id' => $data['id'],
                                'task_id' => !empty($data['taskid']) ? $data['taskid'] : 'on fly task', 
                                'ticket_id' => !empty($data['ticketid']) ? $data['ticketid'] : '', 
                            ];

                            // Call the generateLogActionComment function for 'help_request_solved'
                            $logActionComment = $this->generateLogActionComment('help_request_solved', $logData);
                        } else {
                            $logActionComment = "Manager not found for employee id: {$employee->reports_to}\n";
                        }
                    } else {
                        $logActionComment = "Employee not found for createdby id: {$data['createdby']}\n";
                    }
                    $this->logAction('help_provided', $logActionComment, $data['id'],$data['taskid'],$data['staffId'],get_staff_full_name($data['staffId']),$help_request_data['status'], $help_request_data);
                }else{
                    $notificationMessage = 'Staff changed and help request solved';
                    $this->notifyNewEmployeeHelpRequestClosed($data['taskid'], $data['id'], $data['createdby'], $data['assignTo'], $data['staffId'], $data['pm_remarks']);
                    $help_request_data['helpprovided_at'] = (new DateTime())->format('Y-m-d H:i:s'); 
                    $employee = $this->staff_model->get($data['assignTo'], ['active' => 1]);

                    if ($employee) {
                        $manager = $this->staff_model->get($employee->reports_to, ['active' => 1]);
                        if ($manager) {
                            // Prepare the data array for the 'help_request_solved_parallel_assignment' case
                            $logData = [
                                'manager_name' => $manager->firstname,
                                'manager_id' => $manager->staffid,
                                'new_staff_name' => $employee->firstname,
                                'new_staff_id' => $employee->staffid,
                                'old_staff_id' => $data['createdby'], 
                                'todo_id' => $data['id'],
                                'task_id' => !empty($data['taskid']) ? $data['taskid'] : 'on fly task',
                                'ticket_id' => !empty($data['ticketid']) ? $data['ticketid'] : '',
                            ];

                            $logActionComment = $this->generateLogActionComment('help_request_solved_parallel_assignment', $logData);

                        } else {
                            $logActionComment = "Manager not found for employee id: {$employee->reports_to}\n";
                        }
                    } else {
                        $logActionComment = "Employee not found for assignTo id: {$data['assignTo']}\n";
                    }

                    $this->logAction(
                        'help_provided',
                        $logActionComment,
                        $data['id'],
                        $data['taskid'],
                        $data['staffId'],
                        get_staff_full_name($data['staffId']),
                        $help_request_data['status'],
                        $help_request_data
                    );
                }
                $query = $this->HuddlerTodosModel->update($help_request_data, $id);
                //echo $this->db->last_query()."<hr>" . "\n"; // get last query executed sql
                //exit();
                if ($query) {
                    //echo json_encode(['status' => 'success', 'message' => 'Help request updated successfully']);
                    set_alert('success', $notificationMessage);
                    $previousUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : admin_url('todo/mytodos');
                    redirect($previousUrl);
                } else {
                    //echo json_encode(['status' => 'error', 'message' => 'Failed to update help request']);
                    set_alert('danger', 'Failed to update help request');
                    $previousUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : admin_url('todo/mytodos');
                    redirect($previousUrl);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No data to update']);
            }
        
    }  

    public function manage_access()
    {
        if ($this->input->is_ajax_request()) {
            // Retrieve the arrays of updates
            $updates = $this->input->post('updates');
            $group = $this->input->post('group');
            $success = true;

            try {
                if ($group === 'manage_access') {
                    foreach ($updates as $update) {
                        $employee_id = $update['employee_id'];
                        $reports_to = isset($update['manager_id']) ? $update['manager_id'] : 0;

                        $this->db->update(db_prefix() . 'staff', ['reports_to' => $reports_to], ['staffid' => $employee_id]);

                        if ($this->db->affected_rows() == -1) {
                            throw new Exception('Failed to update manager assignment.');
                        }
                    }
                }

                if ($group === 'assign_pm') {
                    $this->db->update(db_prefix() . 'staff', ['todolist_pm' => 0, 'toplinemanager' => 0]);

                    foreach ($updates as $update) {
                        $employee_id = $update['employee_id'];
                        $assign_as = isset($update['assign_as']) ? $update['assign_as'] : 0;

                        if ($assign_as === 'PM') {
                            $this->db->update(db_prefix() . 'staff', ['todolist_pm' => 1], ['staffid' => $employee_id]);
                        } elseif ($assign_as === 'TL') {
                            $this->db->update(db_prefix() . 'staff', ['toplinemanager' => 1], ['staffid' => $employee_id]);
                        }

                        if ($this->db->affected_rows() == -1) {
                            throw new Exception('Failed to update PM/TL assignment.');
                        }
                    }
                }

                if ($group === 'questions') {
                    $this->update_status($updates);
                }

                echo json_encode(['success' => 'Assignments updated successfully.']);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }

            return;
        }

        // Optional: Handle non-AJAX requests
        // $userRole = $this->staff_model->get_role_by_user_id($this->session->userdata('staff_user_id'));
        // if ($userRole != 2) {
        //     echo "Permission not allowed.";
        //     return;
        // }

        // $this->load->view('manage_access', $data);
    }

    public function update_status($questions)
    {
        $selected_questions = $questions ?? [];

        if (!empty($selected_questions)) {
            $this->QuestionsModel->update_status($selected_questions, 1); // 1 for active
        }

        $this->QuestionsModel->update_status_for_nonselected($selected_questions, 0); // 0 for inactive

    }

    public function settings()
    {
        $userId = $this->session->userdata('staff_user_id');
        $this->load->model('staff_model');
        $staff            =  $this->staff_model->get($userId);
        $istoplinemanager = ($staff->toplinemanager == 1);
        if(!is_admin() && !$istoplinemanager){
            set_alert('danger', 'Access Denied');
            return $this->mytodos();
            exit;
        }
        // Default group value
        $group = $this->input->get('group') ?? 'manage_access';

        // Valid groups
        $validGroups = ['questions', 'manage_access', 'assign_pm'];
        if (!in_array($group, $validGroups)) {
            $group = 'manage_access'; // Default to 'manage_access' if invalid group
        }

        // Initialize data array
        $data = [];
        $data['current_group'] = $group;

        if ($group === 'questions') {
            // Load the QuestionsModel
            $this->load->model('QuestionsModel');
            $data['questions'] = $this->QuestionsModel->get_all();
            $data['view'] = 'questions';
            $data['title'] = 'Questions';
        }elseif($group === 'assign_pm') {
            // Load staff model and check role
            $this->load->model('staff_model');
           // $userRole = $this->staff_model->get_role_by_user_id($this->session->userdata('staff_user_id'));

            
            // Get employee members
            $data['empMembers'] = $this->staff_model->get('', []);
        
            // Fetch all staff members
            $data['staffMembers'] = $this->staff_model->get('', []);
            
            $data['view'] = 'assign_pm';
            $data['title'] = 'Assign PM / TL';
            //  echo "<pre>";print_r($data['empMembers']);exit;
        } else{
            // Load staff model and check role
            $this->load->model('staff_model');
           // $userRole = $this->staff_model->get_role_by_user_id($this->session->userdata('staff_user_id'));

            
            // Get employee members
            $data['empMembers'] = $this->staff_model->get('', []);
        
            // Fetch all staff members
            $data['staffMembers'] = $this->staff_model->get('', []);
            
            $data['view'] = 'manage_access';
            $data['title'] = 'Manage Access';
            //  echo "<pre>";print_r($data['empMembers']);exit;
        }

        // Load the settings view with data
        $this->load->view('settings/settings', $data);
    }

    private function updatetaskstatus($id, $updateData)
    {

        $this->HuddlerTodosModel->update($updateData, $id);
    }
    /**
     * Generates log action comments based on various actions
     *
     * @param string $actionType The type of action (e.g., 'help_request_submitted', 'help_request_solved', etc.)
     * @param array $data An associative array with details about the action (e.g., staff, manager, task, to-do, ticket info)
     * @return string The generated log comment
     */
    function generateLogActionComment($actionType, $data)
    {
        $comment = '';
        
        switch ($actionType) {
            case 'self_assign':
                $comment = "Today's To-Do Plan Submitted by {$data['employee_name']} [Staff_id: {$data['employee_id']}] Successfully.\n";
                $comment .= "Date Time: {$data['submission_time']}\n";
                $comment .= "Task ids in today: " . (!empty($data['today_tasks']) ? implode(', ', $data['today_tasks']) : 'None') . "\n";
                $comment .= "Adhoc ToDo as tasks: " . (!empty($data['adhoc_tasks']) ? implode(', ', $data['adhoc_tasks']) : 'None') . "\n";
                $comment .= "Task ids in overdue: " . (!empty($data['overdue_tasks']) ? implode(', ', $data['overdue_tasks']) : 'None') . "\n";
                $comment .= "Approval due in tasks: " . (!empty($data['approval_due_tasks']) ? implode(', ', $data['approval_due_tasks']) : 'None') . "\n";
                $comment .= "HR Due: " . (!empty($data['hr_due_tasks']) ? implode(', ', $data['hr_due_tasks']) : 'None') . "\n";
                $comment .= "Tasks Needs Attn: " . (!empty($data['tasks_needs_attention']) ? implode(', ', $data['tasks_needs_attention']) : 'None') . "\n";
                $comment .= "Tickets Needs Attn: " . (!empty($data['tickets_needs_attention']) ? implode(', ', $data['tickets_needs_attention']) : 'None') . "\n";
                break;
            case 'help_request_submitted':
                $comment = "Help request submitted by {$data['employee_name']} [Staff_id: {$data['employee_id']}] and sent to manager {$data['manager_name']} [Staff_id: {$data['manager_id']}]\n";
                $comment .= "To Do Id: {$data['todo_id']}\n";
                $comment .= "Task id: " . (!empty($data['task_id']) ? $data['task_id'] : 'on fly task') . "\n";
                $comment .= "Ticket id: " . (!empty($data['ticket_id']) ? $data['ticket_id'] : '') . "\n";
                break;
            
            case 'help_request_solved':
                $comment = "Help request solved by {$data['manager_name']} [Staff_id: {$data['manager_id']}] and sent to staff {$data['employee_name']} [Staff_id: {$data['employee_id']}]\n";
                $comment .= "To Do Id: {$data['todo_id']}\n";
                $comment .= "Task id: " . (!empty($data['task_id']) ? $data['task_id'] : 'on fly task') . "\n";
                $comment .= "Ticket id: " . (!empty($data['ticket_id']) ? $data['ticket_id'] : '') . "\n";
                break;

            case 'help_request_solved_parallel_assignment':
                $comment = "Help request solved by {$data['manager_name']} [Staff_id: {$data['manager_id']}] and parallelly assigned to another staff [{$data['new_staff_name']}] [Staff_id: {$data['new_staff_id']}]\n";
                $comment .= "Assignment removed from staff id: {$data['old_staff_id']}\n";
                $comment .= "To do and related task assigned to: {$data['new_staff_name']} [Staff_id: {$data['new_staff_id']}]\n";
                $comment .= "To Do Id: {$data['todo_id']}\n";
                $comment .= "Task id: " . (!empty($data['task_id']) ? $data['task_id'] : 'on fly task') . "\n";
                $comment .= "Ticket id: " . (!empty($data['ticket_id']) ? $data['ticket_id'] : '') . "\n";
                break;

            case 'staff_changedBy_manager':
                $comment = "Manager {$data['manager_name']} [Staff_id: {$data['manager_id']}] changed the assigned staff of ToDo\n";
                $comment .= "To Do Id: {$data['todo_id']}\n";
                $comment .= "Task id: " . (!empty($data['task_id']) ? $data['task_id'] : 'on fly task') . "\n";
                $comment .= "Previous assigned staff: {$data['old_staff_id']}\n";
                $comment .= "Reassigned to staff: {$data['new_staff_name']} [Staff_id: {$data['new_staff_id']}]\n";
                $comment .= "Assignment of staff in PMP task or Ticket is also changed from staff[{$data['old_staff_name']}] [Staff_id: {$data['old_staff_id']}] to {$data['new_staff_name']} [Staff_id: {$data['new_staff_id']}]\n";
                break;

            case 'task_submitted':
                $comment = "ToDo marked as complete by Staff {$data['employee_name']} [Staff_id: {$data['employee_id']}] and sent for approval to manager {$data['manager_name']} [Staff_id: {$data['manager_id']}]\n";
                $comment .= "To Do Id: {$data['todo_id']}\n";
                $comment .= "Task id: " . (!empty($data['task_id']) ? $data['task_id'] : 'on fly task') . "\n";
                $comment .= "Ticket id: " . (!empty($data['ticket_id']) ? $data['ticket_id'] : '') . "\n";
                $comment .= "Task or Ticket status changed to 'In Testing'\n";
                break;

            case 'todo_rejected':
                $comment = "ToDo rejected by manager {$data['manager_name']} [Staff_id: {$data['manager_id']}] and sent back to staff {$data['employee_name']} [Staff_id: {$data['employee_id']}]\n";
                $comment .= "To Do Id: {$data['todo_id']}\n";
                $comment .= "Task id: " . (!empty($data['task_id']) ? $data['task_id'] : 'on fly task') . "\n";
                $comment .= "Ticket id: " . (!empty($data['ticket_id']) ? $data['ticket_id'] : '') . "\n";
                $comment .= "Task or Ticket status unchanged 'In Testing'\n";
                break;

            case 'todo_approved':
                $comment = "ToDo of staff {$data['employee_name']} [Staff_id: {$data['employee_id']}] approved by manager {$data['manager_name']} [Staff_id: {$data['manager_id']}]\n";
                $comment .= "To Do Id: {$data['todo_id']}\n";
                $comment .= "Task id: " . (!empty($data['task_id']) ? $data['task_id'] : 'on fly task') . "\n";
                $comment .= "Ticket id: " . (!empty($data['ticket_id']) ? $data['ticket_id'] : '') . "\n";
                $comment .= "Task or Ticket response content: [{$data['comment']}]\n";
                $comment .= "Task or Ticket status changed to 'Awaiting Feedback'\n";
                break;

            case 'plan_edited':
                $comment = "ToDo Plan of Staff {$data['employee_name']} [Staff_id: {$data['employee_id']}] for the day {$data['plan_date']} edited by {$data['editor_name']} [Staff_id: {$data['editor_id']}]\n";
                $comment .= "Previous id of table for reference:" . implode(', ', $data['previous_id']) . "\n";
                $comment .= "Current plan Edit Date Time: {$data['edit_time']}\n";
                $comment .= "Task ids in today: " . implode(', ', $data['today_task_ids']) . "\n";
                $comment .= "Adhoc ToDo as tasks: " . implode(', ', $data['adhoc_tasks']) . "\n";
                $comment .= "Task ids in overdue: " . implode(', ', $data['overdue_task_ids']) . "\n";
                $comment .= "Approval due in tasks: " . implode(', ', $data['approval_due_tasks']) . "\n";
                $comment .= "HR Due: " . implode(', ', $data['hr_due']) . "\n";
                $comment .= "Tasks Needs Attn: " . implode(', ', $data['tasks_needs_attention']) . "\n";
                $comment .= "Tickets Needs Attn: " . implode(', ', $data['tickets_needs_attention']) . "\n";
                break;

            case 'mark_as_completed':
                $comment = "Manager {$data['manager_name']} [Staff_id: {$data['manager_id']}] marked the task & todo as 'Complete'\n";
                $comment .= "To Do Id: {$data['todo_id']}\n";
                $comment .= "Task id: " . (!empty($data['task_id']) ? $data['task_id'] : 'on fly task') . "\n";
                $comment .= "Ticket id: " . (!empty($data['ticket_id']) ? $data['ticket_id'] : '') . "\n";
                $comment .= "Comment made on PMP Task/Ticket: [{$data['comment']}]\n";
                $comment .= "Status of PMP Task/Ticket [{$data['task_id']}] is marked as completed.\n";
                break;
            case 'bug_found':
                $comment = "Manager {$data['manager_name']} [Staff_id: {$data['manager_id']}] marked the task & todo as 'Bug Found'\n";
                $comment .= "To Do Id: {$data['todo_id']}\n";
                $comment .= "Task id: " . (!empty($data['task_id']) ? $data['task_id'] : 'on fly task') . "\n";
                $comment .= "Ticket id: " . (!empty($data['ticket_id']) ? $data['ticket_id'] : '') . "\n";
                $comment .= "Comment made on PMP Task/Ticket: [{$data['comment']}]\n";
                $comment .= "Status of PMP Task/Ticket [{$data['task_id']}] is marked as Testing. Related ToDo Id [{$data['todo_id']}] was approved before and now is moved to Re-assigned due to bug found.\n";
                $comment .= "Previous assigned staff: " . (!empty($data['employee_name']) ? $data['employee_name'] : '') ."[Staff_id: ". (!empty($data['employee_id']) ? $data['employee_id'] : '')."]\n";
                if (!empty($data['new_staff_name'])) {
                $comment .= "Reassigned to staff: {$data['new_staff_name']} [Staff_id: {$data['new_staff_id']}]\n";
                }
                if (!empty($data['extra_comment'])) {
                    $comment .= "Extra comment if reassigned: Assignment of staff in PMP task or Ticket is also changed from staff [{$data['prev_staff_name']}] [Staff_id: {$data['prev_staff_id']}] to [{$data['new_staff_name']}] [Staff_id: {$data['new_staff_id']}]\n";
                }
                break;
    
            case 'new_work':
                $comment = "Manager {$data['manager_name']} [Staff_id: {$data['manager_id']}] marked the task & todo as 'New Work'\n";
                $comment .= "To Do Id: {$data['todo_id']}\n";
                $comment .= "Task id: " . (!empty($data['task_id']) ? $data['task_id'] : 'on fly task') . "\n";
                $comment .= "Ticket id: " . (!empty($data['ticket_id']) ? $data['ticket_id'] : '') . "\n";
                $comment .= "Status of PMP Task/Ticket [{$data['task_id']}] is unchanged as 'Complete' (Related ToDo Id [{$data['todo_id']}] was already approved before).\n";
                $comment .= "Manager [{$data['manager_name']}] [Staff_id: {$data['manager_id']}] confirmed that they informed the sales team and also has commented in the task/ticket to client.\n";
                $comment .= "Comment made on PMP Task/Ticket: [{$data['comment']}]\n";
                break;
    
            case 'no_problem':
                $comment = "Manager {$data['manager_name']} [Staff_id: {$data['manager_id']}] marked the task & todo as 'No Problem'\n";
                $comment .= "To Do Id: {$data['todo_id']}\n";
                $comment .= "Task id: " . (!empty($data['task_id']) ? $data['task_id'] : 'on fly task') . "\n";
                $comment .= "Ticket id: " . (!empty($data['ticket_id']) ? $data['ticket_id'] : '') . "\n";
                $comment .= "Status of PMP Task/Ticket [{$data['task_id']}] is unchanged as 'Complete' (Related ToDo Id [{$data['todo_id']}] was already approved before).\n";
                $comment .= "Manager {$data['manager_name']} [Staff_id: {$data['manager_id']}] confirmed that they responded to the client properly and assures that there is no problem.\n";
                $comment .= "Comment made on PMP Task/Ticket: [{$data['comment']}]\n";
                break;
            
            default:
                $comment = "Unknown action type: {$actionType}";
        }

        return $comment;
    }

    /**
     * Check whether the given staff should receive notification for
     * the given task
     *
     * @param  int $staffid
     * @param  int $taskid  [description]
     *
     * @return boolean
     */
    private function should_staff_receive_notification($staffid, $taskid)
    {
        if (!$this->tasks_model->can_staff_access_task($staffid, $taskid)) {
            return false;
        }

        return hooks()->apply_filters('should_staff_receive_task_notification', ($this->tasks_model->is_task_assignee($staffid, $taskid)
            || $this->tasks_model->is_task_follower($staffid, $taskid)
            || $this->tasks_model->is_task_creator($staffid, $taskid)
            || $this->tasks_model->staff_has_commented_on_task($staffid, $taskid)), ['staff_id' => $staffid, 'task_id' => $taskid]);
    }
    private function logAction($actionType, $comment, $todoId, $taskId, $staffId, $staff_name, $todo_status, $additionalData = []) {
        // Prepare log data
        $logData = [
            'huddlertodo_id'  => $todoId,
            'comment'         => $comment,
            'task_id'         => $taskId,
            'staff_id'        => $staffId,
            'staff_name'      => $staff_name,
            'todo_status'     => $todo_status,
            'action_type'     => $actionType,
            'timestamp'       => date('Y-m-d H:i:s'),
            'formdata_json'   => is_string($additionalData) && json_decode($additionalData) !== null ? $additionalData : json_encode($additionalData) // Encode only if not valid JSON
        ];

        // Insert log into database directly
        $this->db->insert('tblhuddleraction_logs', $logData);
    }
    private function notifyStaffOfRejection($taskId, $staffId,$questionsData) {
        $this->load->model('staff_model');
        $staff = $this->staff_model->get($staffId, ['active' => 1]);

        $empAnswersquery = $this->db->select('formdata_json, huddlertodo_id')
                                   ->where('todo_status', 3)
                                   ->where('staff_id', $staffId)
                                   ->where('task_id', $taskId)
                                   ->group_by('task_id')
                                   ->get('tblhuddleraction_logs');
        $empAnswersresult = $empAnswersquery->row(); 
        $formdata = $empAnswersresult->formdata_json;
        $empAnswerformdata = $formdata;
        $huddlerTodoID = $empAnswersresult->huddlertodo_id;
        $managerComments = 'No comments found.'; // Default message
       
        if ($questionsData) {
            $additionalData = json_decode($questionsData, true);
            $managerComments = $additionalData['bugfoundComments12'] ?? 'No comments provided.';
        }
        if ($this->should_staff_receive_notification($staff->staffid, $taskId)) {
            
        }
            $taskDetails = $this->tasks_model->get($taskId);
            $emailMessage = $this->createRejectionEmail($staff->full_name, $huddlerTodoID, $taskDetails, $managerComments, $empAnswerformdata);
           // $this->load->model('emails_model');
            // return $emailMessage;
            $this->emails_model->send_simple_email($staff->email, "HuddlerTodo Status Changed", $emailMessage);
        
    }
    private function createRejectionEmail($fullName, $todoDetails, $taskDetails, $managerComments, $questionsData)
    {
        $taskUrl = admin_url('todo/ViewMyTodos');
        $questionsHtml = $this->formatQuestionsHtml($questionsData);

        return "
            <p>Dear " . htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') . ",</p>
            <p><strong>Your submitted To-Do / Task got rejected by the PM or TL.<strong></p>
            <ul style=\"list-style-type: none; padding-left: 0;\">
                <li><strong>To-Do ID:</strong> " . htmlspecialchars($todoDetails, ENT_QUOTES, 'UTF-8') . "</li>
                <li><strong>To-Do Title/Name:</strong> " . htmlspecialchars($taskDetails->name, ENT_QUOTES, 'UTF-8') . "</li>
                <li><strong>Related Task ID:</strong> " . htmlspecialchars($taskDetails->id, ENT_QUOTES, 'UTF-8') . "</li>
                <li><strong>Task Name:</strong> " . htmlspecialchars($taskDetails->name, ENT_QUOTES, 'UTF-8') . "</li>
            </ul>
            <p><strong>Manager comments:</strong></p>
            <p>" . nl2br(htmlspecialchars($managerComments, ENT_QUOTES, 'UTF-8')) . "</p>
            <hr>
            <p><strong>Copy of your submission form:</strong></p>
            " . $questionsHtml . "
            <p>You can review the task and the Project Manager's comments by clicking on the link below:</p>
            <p><a href='" . htmlspecialchars($taskUrl, ENT_QUOTES, 'UTF-8') . "'>View Task Details</a></p>
            <p>Best regards,</p>
            <p><strong>PMP Team (VK)</strong></p>
        ";
    }

    private function formatQuestionsHtml($questionsData)
    {
        $questions = json_decode($questionsData, true);
        $questionsHtml = '<form class="col-md-6" id="answersForm" style="">';
        foreach ($questions as $questionId => $answer) {
            if (strpos($questionId, 'question_') !== false) {
                $numericId = str_replace('question_', '', $questionId);
                $questionText = $this->QuestionsModel->get_by_id($numericId);

                if ($questionText) {
                    $questionsHtml .= '<div class="form-group">';
                    $questionsHtml .= '<label class="question_text">' . htmlspecialchars($questionText['question_text'], ENT_QUOTES, 'UTF-8') . '</label>';
                    $questionsHtml .= '<div class="tw-flex tw-items-center">';
                
                    if ($questionText['options'] === 'Yes / No') {
                        $questionsHtml .= '<label><input type="radio" name="' . htmlspecialchars($questionId, ENT_QUOTES, 'UTF-8') . '" value="Yes" disabled' . ($answer === 'Yes' ? ' checked' : '') . '> Yes</label>';
                        $questionsHtml .= ' <label><input type="radio" name="' . htmlspecialchars($questionId, ENT_QUOTES, 'UTF-8') . '" value="No" disabled' . ($answer === 'No' ? ' checked' : '') . '> No</label>';
                    } elseif ($questionText['options'] === '[url]') {
                        $questionsHtml .= '<input type="url" name="' . htmlspecialchars($questionId, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($answer, ENT_QUOTES, 'UTF-8') . '" disabled class="form-control" style="width:80%;">';
                    } elseif ($questionText['options'] === '[description box]') {
                        $questionsHtml .= '<textarea name="' . htmlspecialchars($questionId, ENT_QUOTES, 'UTF-8') . '" disabled class="form-control">' . htmlspecialchars($answer, ENT_QUOTES, 'UTF-8') . '</textarea>';
                    }

                    $questionsHtml .= '</div></div>';
                }
            }
        }

        // If there's a video link, include it
        if (!empty($questions['video_link'])) {
            $questionsHtml .= '<div class="form-group"><label class="question_text">Loom Video/Screenshot URL for proof of work to submit to client</label>';
            $questionsHtml .= '<a href="' . htmlspecialchars($questions['video_link'], ENT_QUOTES, 'UTF-8') . '" target="_blank">' . htmlspecialchars($questions['video_link'], ENT_QUOTES, 'UTF-8') . '</a></div>';
        }

        $questionsHtml .= '</form>';

        return $questionsHtml;
    }
    private function notifyManagerHelpRequested($taskId, $todoId, $staffId, $helpRequestData) 
    {

        // Get employee details
        $employee = $this->staff_model->get($staffId, ['active' => 1]);
    
        // Get manager details
        $manager = $this->staff_model->get($employee->reports_to, ['active' => 1]);
    
        // Get task and to-do details
        $taskDetails = $this->tasks_model->get($taskId);
        $todoDetails = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id($staffId, ['id' => $todoId]);
        // echo $this->db->last_query().'/n';
        // print_r($todoDetails);
        // exit();
        $todoDetails = $todoDetails[0];
        // Get help request details
        $comment = $helpRequestData['helpreq_remarks'] ?? 'No comment provided.';
        $videoLink = $helpRequestData['video_link'] ?? 'No video link provided.';
        if ($manager) {
            $emailMessage = $this->createHelpRequestEmail($manager->full_name, $employee->full_name, $comment, $videoLink, $taskDetails, $todoDetails);
            //$this->load->model('emails_model');
            $this->emails_model->send_simple_email($manager->email, "Help Request on To-Do/Task", $emailMessage);
        }
    }
    
    private function createHelpRequestEmail($managerName, $employeeName, $comment, $videoLink, $taskDetails, $todoDetails) 
    {
        return "
            <p>Dear " . htmlspecialchars($managerName, ENT_QUOTES, 'UTF-8') . ",</p>
            <p> <strong>" . htmlspecialchars($employeeName, ENT_QUOTES, 'UTF-8') . " needs help on a To-Do/Task. </strong></p>
            <p><strong><u>Help request details:</u></strong></p>
            <ul style='list-style-type: none; padding-left: 0;'>
                <li><strong>To-Do ID:</strong> " . htmlspecialchars($todoDetails['id'], ENT_QUOTES, 'UTF-8') . "</li>
                <li><strong>To-Do Title/Name:</strong> " . htmlspecialchars($todoDetails['task_name'], ENT_QUOTES, 'UTF-8') . "</li>
                <li><strong>Related Task ID:</strong> " . htmlspecialchars($taskDetails->id, ENT_QUOTES, 'UTF-8') . "</li>
                <li><strong>Task Name:</strong> " . htmlspecialchars($taskDetails->name, ENT_QUOTES, 'UTF-8') . "</li>
            </ul>
            <p><strong><u>Comment or Explanation:</u></strong></p>
            <p>" . nl2br(htmlspecialchars($comment, ENT_QUOTES, 'UTF-8')) . "</p>
            <p><strong><u>Loom Video/Screenshot link by employee:</u></strong> <a href='" . htmlspecialchars($videoLink, ENT_QUOTES, 'UTF-8') . "' target='_blank'>" . htmlspecialchars($videoLink, ENT_QUOTES, 'UTF-8') . "</a></p>
            
            <p>Best Regards,</p>
            <p>PMP Team (VK)</p>
        ";
    }
    private function notifyEmployeeHelpRequestClosed($taskId, $todoId, $staffId, $managerId, $managerComment) 
    {

        // Get employee details
        $employee = $this->staff_model->get($staffId, ['active' => 1]);
    
        // Get manager details
        $manager = $this->staff_model->get($managerId, ['active' => 1]);
    
        // Get task details
        $taskDetails = $this->tasks_model->get($taskId);
        
        // Get to-do details using staff ID and additional condition if required
        $todoDetails = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id($staffId, ['id' => $todoId]);
    
        if (!empty($todoDetails)) {
            $todoDetails = $todoDetails[0]; // Use the first (and likely only) item in the result array
    
            $comment = $todoDetails['helpreq_remarks'] ?? 'No comment provided.';
            $videoLink = $todoDetails['video_link'] ?? 'No video link provided.';
    
            $emailMessage = $this->createHelpRequestClosureEmailForSameEmployee(
                $employee->full_name,
                $manager->full_name,
                $comment,
                $videoLink,
                $taskDetails,
                $todoDetails,
                $managerComment
            );
            //$this->load->model('emails_model');
            $this->emails_model->send_simple_email($employee->email, "Your Help Request Has Been Closed", $emailMessage);
        } else {
            log_message('error', "No to-do details found for staff ID {$staffId} and to-do ID {$todoId}.");
        }
    }
    
    private function createHelpRequestClosureEmailForSameEmployee($employeeName, $managerName, $comment, $videoLink, $taskDetails, $todoDetails, $managerComment) 
    {
        return "
            <p>Dear " . htmlspecialchars($employeeName, ENT_QUOTES, 'UTF-8') . ",</p>
            <p>Your TL or PM " . htmlspecialchars($managerName, ENT_QUOTES, 'UTF-8') . " closed your help request with comments.</p>
            <p><u>Help request details:</u></p>
            <ul style='list-style-type: none; padding-left: 0;'>
                <li><strong>To-Do ID:</strong> " . htmlspecialchars($todoDetails['id'], ENT_QUOTES, 'UTF-8') . "</li>
                <li><strong>To-Do Title/Name:</strong> " . htmlspecialchars($todoDetails['task_name'], ENT_QUOTES, 'UTF-8') . "</li>
                <li><strong>Related Task ID:</strong> " . htmlspecialchars($taskDetails->id, ENT_QUOTES, 'UTF-8') . "</li>
                <li><strong>Task Name:</strong> " . htmlspecialchars($taskDetails->name, ENT_QUOTES, 'UTF-8') . "</li>
            </ul>
            <p><u>Your Comment or Explanation:</u></p>
            <p>" . nl2br(htmlspecialchars($comment, ENT_QUOTES, 'UTF-8')) . "</p>
            <p><u>Loom video link provided by you:</u> <a href='" . htmlspecialchars($videoLink, ENT_QUOTES, 'UTF-8') . "' target='_blank'>" . htmlspecialchars($videoLink, ENT_QUOTES, 'UTF-8') . "</a></p>
            <p><strong>Manager Comments on Help Request Closure:</strong></p>
            <p>" . nl2br(htmlspecialchars($managerComment, ENT_QUOTES, 'UTF-8')) . "</p>
            <p>Best Regards,</p>
            <p><strong>PMP Team (VK)</strong></p>
        ";
    }
    private function notifyNewEmployeeHelpRequestClosed($taskId, $todoId, $oldStaffId, $newStaffId, $managerId, $managerComment) {
        $this->load->model('staff_model');
        $this->load->model('tasks_model');
        $this->load->model('HuddlerTodosModel');
        
        // Get old employee details
        $oldEmployee = $this->staff_model->get($oldStaffId, ['active' => 1]);
    
        // Get new employee details
        $newEmployee = $this->staff_model->get($newStaffId, ['active' => 1]);
    
        // Get manager details
        $manager = $this->staff_model->get($managerId, ['active' => 1]);
    
        // Get task details
        $taskDetails = $this->tasks_model->get($taskId);
        
        // Get to-do details using staff ID and additional condition if required
        $todoDetails = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id($oldStaffId, ['id' => $todoId]);
    
        if (!empty($todoDetails)) {
            $todoDetails = $todoDetails[0]; // Use the first (and likely only) item in the result array
    
            $comment = $todoDetails['helpreq_remarks'] ?? 'No comment provided.';
            $videoLink = $todoDetails['video_link'] ?? 'No video link provided.';
    
            $emailMessage = $this->createHelpRequestClosureEmailForNewEmployee(
                $newEmployee->full_name,
                $manager->full_name,
                $oldEmployee->full_name,
                $comment,
                $videoLink,
                $taskDetails,
                $todoDetails,
                $managerComment
            );
            //$this->load->model('emails_model');
            $this->emails_model->send_simple_email($newEmployee->email, "New To-Do Assigned After Help Request Closure", $emailMessage);
        } else {
            log_message('error', "No to-do details found for old staff ID {$oldStaffId} and to-do ID {$todoId}.");
        }
    }
    private function createHelpRequestClosureEmailForNewEmployee($newEmployeeName, $managerName, $oldEmployeeName, $comment, $videoLink, $taskDetails, $todoDetails, $managerComment) {
        return "
            <p>Dear " . htmlspecialchars($newEmployeeName, ENT_QUOTES, 'UTF-8') . ",</p>
            <p>Your TL or PM " . htmlspecialchars($managerName, ENT_QUOTES, 'UTF-8') . " closed a help request with comments and new To-Do assigned to you.</p>
            <p><u>Past Help request details:</u></p>
            <ul style='list-style-type: none; padding-left: 0;'>
                <li><strong>To-Do ID:</strong> " . htmlspecialchars($todoDetails['id'], ENT_QUOTES, 'UTF-8') . "</li>
                <li><strong>To-Do Title/Name:</strong> " . htmlspecialchars($todoDetails['task_name'], ENT_QUOTES, 'UTF-8') . "</li>
                <li><strong>Related Task ID:</strong> " . htmlspecialchars($taskDetails->id, ENT_QUOTES, 'UTF-8') . "</li>
                <li><strong>Task Name:</strong> " . htmlspecialchars($taskDetails->name, ENT_QUOTES, 'UTF-8') . "</li>
            </ul>
            <p><strong>Help request created by:</strong> " . htmlspecialchars($oldEmployeeName, ENT_QUOTES, 'UTF-8') . "</p>
            <p><u>Help Request Comment or Explanation:</u></p>
            <p>" . nl2br(htmlspecialchars($comment, ENT_QUOTES, 'UTF-8')) . "</p>
            <p><u>Loom Video/Screenshot link provided in Help Request:</u> <a href='" . htmlspecialchars($videoLink, ENT_QUOTES, 'UTF-8') . "' target='_blank'>" . htmlspecialchars($videoLink, ENT_QUOTES, 'UTF-8') . "</a></p>
            <p><strong>Manager Comments on Help Request Closure:</strong></p>
            <p>" . nl2br(htmlspecialchars($managerComment, ENT_QUOTES, 'UTF-8')) . "</p>
            <p>Now, this To-Do is assigned to you and your attention and help is needed to solve and complete this To-Do and Task. It is now in your Goal.</p>
            <p>Best Regards,</p>
            <p><strong>PMP Team (VK)</strong></p>
        ";
    }

    public function GetTaskPoint($taskId) {
        $query = $this->db->select('VALUE')
                          ->from('tblcustomfieldsvalues')
                          ->where([
                              'relid' => (int)$taskId, 
                              'fieldid' => 9,
                              'fieldto' => 'tasks'
                          ])
                          ->get();
    
        if ($query->num_rows() > 0) {
            return $query->row()->VALUE;
        } else {
            return null; 
        }
    }
    public function GetInternalEndDate($taskId) {
        $query = $this->db->select('VALUE')
                          ->from('tblcustomfieldsvalues')
                          ->where([
                              'relid' => (int)$taskId, 
                              'fieldid' => 7,
                              'fieldto' => 'tasks'
                          ])
                          ->get();
    
        if ($query->num_rows() > 0) {
            return $query->row()->VALUE;
        } else {
            return null; 
        }
    }
    
    public function GetStaffTaskPoints($staffId) {
        if (empty($staffId)) {
            return [
                'Points_Assigned_Today' => 0,
                'Earned_This_Month' => 0,
                'Earned_This_Year' => 0,
                'Points_Due' => 0
            ];
        }
    
        // Fetch Task IDs for today's assigned tasks
        $AssignTodayHuddlerTodo = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id($staffId, ['status' => 2]);
        $taskIdsToday = array_map(fn($todo) => $todo['task_id'], $AssignTodayHuddlerTodo);
        $pointsAssignedToday = $this->getAlltaskPoints($taskIdsToday);
    
        // Points Earned in This Month
        $firstDayOfMonth = date('Y-m-01 00:00:00');
        $lastDayOfMonth = date('Y-m-t 23:59:59');
        $PMapprovedHuddlerTodo = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id( $staffId, ['status' => 4, "date_added BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth'"]);
        $taskIdsMonth = array_map(fn($todo) => $todo['task_id'], $PMapprovedHuddlerTodo);
        $taskPoint = (float) $this->getAlltaskPoints($taskIdsMonth);
        $negativePoint = (float) $this->getNegativePoints($taskIdsMonth);
        $pointsEarnedMonth = $taskPoint - $negativePoint;
        $pointsEarnedMonth = number_format($pointsEarnedMonth, 2, '.', '');
    
        // Points Earned This Year
        $firstDayOfYear = date('Y-01-01 00:00:00');
        $currentDay = date('Y-m-d H:i:s');
        $PMapprovedHuddlerTodoYear = $this->HuddlerTodosModel->get_huddler_todos_by_staff_id($staffId, ['status' => 4, "date_added BETWEEN '$firstDayOfYear' AND '$currentDay'"]);
        $taskIdsYear = array_map(fn($todo) => $todo['task_id'], $PMapprovedHuddlerTodoYear);
        $taskPointYear = (float) $this->getAlltaskPoints($taskIdsYear);
        $negativePointYear = (float) $this->getNegativePoints($taskIdsYear);
        $pointsEarnedYear = $taskPointYear - $negativePointYear;
        $pointsEarnedYear = number_format($pointsEarnedYear, 2, '.', '');
    
        // Points Due Calculation
        $pointsDue = 925 - $pointsEarnedYear;
    
        // Return the calculated points
        return [
            'Points_Assigned_Today' => $pointsAssignedToday,
            'Earned_This_Month' => $pointsEarnedMonth,
            'Earned_This_Year' => $pointsEarnedYear,
            'Points_Due' => $pointsDue
        ];
    }
    public function GetStaffQualitativeReport($staffId) {
        if (empty($staffId)) {
            return [
                'On_Time' => ['ratio' => '0/0', 'percentage' => '0.00%'],
                'Reasonable_Delay' => ['ratio' => '0/0', 'percentage' => '0.00%'],
                'Delay' => ['ratio' => '0/0', 'percentage' => '0.00%']
            ];
        }
    
        // Fetch tasks assigned to the staff
        $today = date('Y-m-d H:i:s');
        $allTasks = $this->tasks_model->get_tasks_by_staff_id($staffId, ['datefinished <=' => $today]);
    
        $onTime = 0;
        $reasonableDelay = 0;
        $delay = 0;
        $totalTasks = count($allTasks);
        $internalEndDate= '';
        $afMoveDate = '';
    
        foreach ($allTasks as $task) {
            $taskId = $task['id'];
            $projectEndDate = $task['duedate'] ?? null; // Handle missing project end date
    
            // Get the AF move date (date added when moved to status = 4)
            $afMoveDateQuery = $this->db->select('date_added')
                ->from('tblhuddlertodos')
                ->where(['task_id' => $taskId, 'status' => 4])
                ->get();
            $afMoveDate = $afMoveDateQuery->num_rows() > 0 ? $afMoveDateQuery->row()->date_added : null;
            
    
            if (!$afMoveDate) {
                continue; // If no AF move date, skip the task
            }
    
            // Get the internal end date for the task
            $internalEndDate = $this->GetInternalEndDate($taskId);
            if (!$internalEndDate) {
                continue; // If no internal end date found, skip the task
            }
    
            // Convert dates to proper format
            $internalEndDate = date('Y-m-d', strtotime($internalEndDate));
            $afMoveDate = date('Y-m-d', strtotime($afMoveDate));
    
            // Classification Logic
            if ($afMoveDate <= $internalEndDate) {
                $onTime++;
            } elseif (
                $afMoveDate <= date('Y-m-d', strtotime($internalEndDate . ' +2 days')) ||
                ($projectEndDate && $afMoveDate <= $projectEndDate)
            ) {
                $reasonableDelay++;
            } else {
                $delay++;
            }
        }
    
        // Calculate percentages
        $onTimePercentage = $totalTasks ? number_format(($onTime / $totalTasks) * 100, 2) : '0.00';
        $reasonableDelayPercentage = $totalTasks ? number_format(($reasonableDelay / $totalTasks) * 100, 2) : '0.00';
        $delayPercentage = $totalTasks ? number_format(($delay / $totalTasks) * 100, 2) : '0.00';
    
        // Build the report
        return [
            'On_Time' => [
                'ratio' => "$onTime/$totalTasks",
                'percentage' => "{$onTimePercentage}%"
            ],
            'Reasonable_Delay' => [
                'ratio' => "$reasonableDelay/$totalTasks",
                'percentage' => "{$reasonableDelayPercentage}%"
            ],
            'Delay' => [
                'ratio' => "$delay/$totalTasks",
                'percentage' => "{$delayPercentage}%"
            ]
        ];
    }
    
    private function getAlltaskPoints($taskIds) {
        if (empty($taskIds)) return 0;
        $query = $this->db->select_sum('VALUE')
                          ->from('tblcustomfieldsvalues')
                          ->where_in('relid', $taskIds)
                          ->where('fieldid', 9)
                          ->where('fieldto', 'tasks')
                          ->get();
        return $query->row()->VALUE ?? 0;
    }
    
    private function getNegativePoints($taskIds) {
        if (empty($taskIds)) return 0;
        $query = $this->db->select_sum('negativepoint')
                          ->from('tblnegativepoint')
                          ->where_in('task_id', $taskIds)
                          ->get();
        return $query->row()->negativepoint ?? 0;
    }
    
    /**
     * Update negative points for a task.
     * 
     * @param int|string $taskid The task ID.
     * @param int|string $todoid The to-do ID.
     * @param int $staffid The Staff ID.
     * @param int $managerid The PM/TL ID.
     */
    public function updateNegativePoints($taskid, $todoid, $staffid, $managerid)
    {   
        // Fetch the negative points data from the database
        $negativePoint = $this->calculateNegativePoint($taskid, $todoid);
        $taskPoint = $this->GetTaskPoint($taskid);
        $taskRejectedCount = $this->db->select('COUNT(task_id) as count')
                                  ->from('tblhuddleraction_logs')
                                  ->where('task_id', $taskid)
                                  ->where('action_type', 'on_rejected')
                                  ->get()
                                  ->row()
                                  ->count;
                                  
        $negativePointData = [
            'todo_id'                => $todoid ?? 0,
            'task_id'                => $taskid ?? 0,
            'staff_id'               => $staffid ?? 0,
            'staffname'              => get_staff_full_name($staffid ?? ''),
            'reassigned_fromstaffid' => $managerid ?? 0,
            'reassignedfromname'     => get_staff_full_name($managerid ?? ''),
            'task_point'             => $taskPoint ?? 0.0,
            'taskrejectioncount'     => $taskRejectedCount ?? 0,
            'negativepoint'          => $negativePoint ?? 0.0
        ];

        $this->db->where('todo_id', $todoid)
                ->where('staff_id', $staffid)
                ->delete('tblnegativepoint');
        // Insert into the database
        $this->db->insert('tblnegativepoint', $negativePointData);
    }

    /**
     * Calculate the negative points for a task based on rejection and bug report counts.
     *
     * @param int|string $taskid The task ID.
     * @param int|string $todoid The to-do ID.
     * @return float The calculated negative points.
     */
    private function calculateNegativePoint($taskid, $todoid)
    {
        $taskRejectedCount = $this->db->select('COUNT(task_id) as count')
                                    ->from('tblhuddleraction_logs')
                                    ->where('task_id', $taskid)
                                    ->where('action_type', 'on_rejected')
                                    ->get()
                                    ->row()
                                    ->count;

        $taskBugreportedCount = $this->db->select('COUNT(task_id) as count')
                                        ->from('tblhuddleraction_logs')
                                        ->where('task_id', $taskid)
                                        ->where('action_type', 'need_attention_bug')
                                        ->get()
                                        ->row()
                                        ->count;

        $taskPoint           = $this->GetTaskPoint($taskid);
        $taskInternalEndDate = $this->GetInternalEndDate($taskid);
        $todoCurrentStatus   = $this->HuddlerTodosModel->get_status_by_id($todoid);
        $today               = date('Y-m-d');
        
        $negativePoint = 0;

        if ($taskPoint > 0 && $taskInternalEndDate < $today) {
            if ($taskRejectedCount > 1) {
                $negativePoint = (($taskRejectedCount - 1) * 50 / 100) * $taskPoint;
            }
            if ($taskBugreportedCount > 1 && ($taskInternalEndDate < $today || $todoCurrentStatus == 5)) {
                $negativePoint = (($taskBugreportedCount - 1) * 50 / 100) * $taskPoint;
            }
        }

        return round($negativePoint, 2); 
    }
    function formatTaskForEmail($task) {
        return [
            'id' => $task['task_id'],
            'name' => $task['task_name'], 
            'project' => $task['project_name'] ?? '', 
            'todoID' => $task['id'], 
            'status' => $task['status'] 
        ];
    }
    /**
     * Create SOD (Start of Day) email for an employee.
     *
     * @param string $fullName The full name of the employee receiving the email.
     * @param string $sodDate The date of the SOD report (e.g., "September 10th, 2024").
     * @param array $tasks An array of task objects containing details for each task.
     *                      Each task object should have the following properties:
     *                      - id (int): The ID of the task.
     *                      - name (string): The name or title of the task.
     *                      - project (string): The project reference or name.
     *                      - todoID (int): The related To-Do ID.
     *                      - status (string): The current status of the task (e.g., "Overdue", "Today").
     * @return string The HTML content for the SOD email.
     */
    private function createSODEmailForEmployee($fullName, $sodDate, $tasks)
    {
        // Construct the table for task details
        $taskRows = '';
        foreach ($tasks as $task) {
            $taskRows .= "
                <tr>
                    <td>" . htmlspecialchars($task['id'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['name'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['project'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['todoID'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') . "</td>
                </tr>";
        }

        // Email body content
        return "
            <p>Hi " . htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') . ",</p>
            <p>Your Start of Day report for " . htmlspecialchars($sodDate, ENT_QUOTES, 'UTF-8') . " is submitted.</p>
            <p><u><strong>The submitted work plan is as follows:</strong></u></p>
            <p><strong>Start of Day report of:</strong> " . htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') . "</p>
            <p><strong>SOD Report date:</strong> " . htmlspecialchars($sodDate, ENT_QUOTES, 'UTF-8') . "</p><br>
            
            <table border=\"1\" cellpadding=\"5\" cellspacing=\"0\" style=\"border-collapse: collapse; width: 100%;\">
                <thead>
                    <tr>
                       <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Task ID</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Task Name</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Ref Project</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Ref ToDoID</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Status</th>
                    </tr>
                </thead>
                <tbody>
                    " . $taskRows . "
                </tbody>
            </table>

            <p> <b>You are expected to complete all the above tasks that you have self-assigned for yourself and/or are due today.</b></p><br>

            <p>Thanks,</p>
            <p>PMP Team</p>
        ";
    }
    /**
     * Create SOD (Start of Day) email for a manager.
     *
     * @param string $mgrName The full name of the manager receiving the email.
     * @param string $empFirstName The first name of the employee submitting the SOD report.
     * @param int $staffId The ID of the employee submitting the report.
     * @param string $sodDate The date of the SOD report (e.g., "September 10th, 2024").
     * @param array $tasks An array of task objects containing details for each task.
     *                      Each task object should have the following properties:
     *                      - id (int): The ID of the task.
     *                      - name (string): The name or title of the task.
     *                      - project (string): The project reference or name.
     *                      - todoID (int): The related To-Do ID.
     *                      - status (string): The current status of the task (e.g., "Overdue", "Today").
     * @return string The HTML content for the SOD email to the manager.
     */
    private function createSODEmailForManager($mgrName, $empFirstName, $staffId, $sodDate, $tasks)
    {
        // Construct the table for task details
        $taskRows = '';
        foreach ($tasks as $task) {
            $taskRows .= "
                <tr>
                    <td>" . htmlspecialchars($task['id'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['name'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['project'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['todoID'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') . "</td>
                </tr>";
        }

        // Email body content
        return "
            <p>Hi " . htmlspecialchars($mgrName, ENT_QUOTES, 'UTF-8') . ",</p>
            <p>Start of Day report for " . htmlspecialchars($sodDate, ENT_QUOTES, 'UTF-8') . " is submitted by " . htmlspecialchars($empFirstName, ENT_QUOTES, 'UTF-8') . " (Staff ID: " . htmlspecialchars($staffId, ENT_QUOTES, 'UTF-8') . ").</p>
            <p><u><strong>The submitted work plan is as follows: </strong></u></p>
            <p><strong>Start of Day report of:</strong> " . htmlspecialchars($empFirstName, ENT_QUOTES, 'UTF-8') . "</p>
            <p><strong>SOD Report date:</strong> " . htmlspecialchars($sodDate, ENT_QUOTES, 'UTF-8') . "</p><br>

            <table border=\"1\" cellpadding=\"5\" cellspacing=\"0\" style=\"border-collapse: collapse; width: 100%;\">
                <thead>
                    <tr>
                       <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Task ID</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Task Name</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Ref Project</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Ref ToDoID</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Status</th>
                    </tr>
                </thead>
                <tbody>
                    " . $taskRows . "
                </tbody>
            </table>

            <p><strong>You and the staff are expected to complete all the above tasks that are self-assigned by the staff or assigned by you and/or are due today. </strong></p><br>

            <p>Thanks,</p>
            <p>PMP Team</p>
        ";
    }
    /**
     * Create email notification for when an employee modifies their work plan during the day.
     *
     * @param string $fullName The full name of the employee receiving the email.
     * @param string $modificationTime The date and time when the work plan was modified (e.g., "September 10th, 2024 at hh:mm:ss IST").
     * @param array $tasks An array of task objects containing details for each task.
     *                      Each task object should have the following properties:
     *                      - id (int): The ID of the task.
     *                      - name (string): The name or title of the task.
     *                      - project (string): The project reference or name.
     *                      - todoID (int): The related To-Do ID.
     *                      - status (string): The current status of the task (e.g., "Overdue", "Today", "Pending Approval").
     * @return string The HTML content for the email notifying about the modified work plan.
     */
    private function createModifiedWorkPlanEmailForEmployee($fullName, $modificationTime, $tasks)
    {
        // Construct the table for task details
        $taskRows = '';
        foreach ($tasks as $task) {
            $taskRows .= "
                <tr>
                    <td>" . htmlspecialchars($task['id'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['name'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['project'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['todoID'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') . "</td>
                </tr>";
        }

        // Email body content
        return "
            <p>Hi " . htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') . ",</p>
            <p>You modified your Today’s Work Plan on " . htmlspecialchars($modificationTime, ENT_QUOTES, 'UTF-8') . ".</p>
            <p><u><strong>The updated work plan is as follows:</strong></u></p>
            <p><strong>Modified Work Plan report of:</strong> " . htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') . "</p>
            <p><strong>Modified Work Plan as on:</strong> " . htmlspecialchars($modificationTime, ENT_QUOTES, 'UTF-8') . "</p>

            <table border=\"1\" cellpadding=\"5\" cellspacing=\"0\" style=\"border-collapse: collapse; width: 100%;\">
                <thead>
                    <tr>
                       <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Task ID</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Task Name</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Ref Project</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Ref ToDoID</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Status</th>
                    </tr>
                </thead>
                <tbody>
                    " . $taskRows . "
                </tbody>
            </table>

            <p><strong>You are expected to complete all the above tasks that you have self-assigned for yourself and/or are due today. </strong></p>

            <p>Thanks,</p>
            <p>PMP Team</p>
        ";
    }
    /**
     * Create email notification for a manager when an employee modifies their work plan.
     *
     * @param string $mgrName The full name of the manager receiving the email.
     * @param string $staffName The full name of the employee who modified the work plan.
     * @param int $staffId The ID of the employee who modified the work plan.
     * @param string $modificationTime The date and time when the work plan was modified (e.g., "September 10th, 2024 at hh:mm:ss IST").
     * @param array $tasks An array of task objects containing details for each task.
     *                      Each task object should have the following properties:
     *                      - id (int): The ID of the task.
     *                      - name (string): The name or title of the task.
     *                      - project (string): The project reference or name.
     *                      - todoID (int): The related To-Do ID.
     *                      - status (string): The current status of the task (e.g., "Overdue", "Today", "Pending Approval").
     * @return string The HTML content for the email notifying the manager about the modified work plan.
     */
    private function createModifiedWorkPlanEmailForManager($mgrName, $staffName, $staffId, $modificationTime, $tasks)
    {
        // Construct the table for task details
        $taskRows = '';
        foreach ($tasks as $task) {
            $taskRows .= "
                <tr>
                    <td>" . htmlspecialchars($task['id'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['name'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['project'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['todoID'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') . "</td>
                </tr>";
        }

        // Email body content
        return "
            <p>Hi " . htmlspecialchars($mgrName, ENT_QUOTES, 'UTF-8') . ",</p>
            <p>" . htmlspecialchars($staffName, ENT_QUOTES, 'UTF-8') . " (Staff ID: " . htmlspecialchars($staffId, ENT_QUOTES, 'UTF-8') . ") modified their Today’s Work Plan at " . htmlspecialchars($modificationTime, ENT_QUOTES, 'UTF-8') . ".</p>
            <p><u><strong>The updated work plan is as follows:</strong> </u></p>
            <p><strong>Modified Work Plan report of:</strong> " . htmlspecialchars($staffName, ENT_QUOTES, 'UTF-8') . "</p>
            <p><strong>Modified Work Plan as on:</strong> " . htmlspecialchars($modificationTime, ENT_QUOTES, 'UTF-8') . "</p>

            <table border=\"1\" cellpadding=\"5\" cellspacing=\"0\" style=\"border-collapse: collapse; width: 100%;\">
                <thead>
                    <tr>
                       <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Task ID</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Task Name</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Ref Project</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Ref ToDoID</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Status</th>
                    </tr>
                </thead>
                <tbody>
                    " . $taskRows . "
                </tbody>
            </table>

            <p><strong> You and the staff are expected to complete all the above tasks that are self-assigned by the staff or assigned by you and/or are due today. </strong></p>

            <p>Thanks,</p>
            <p>PMP Team</p>
        ";
    }
    /**
     * Create email notification for a manager when they modify an employee's work plan.
     *
     * @param string $mgrName The full name of the manager.
     * @param string $staffName The full name of the employee whose work plan was modified.
     * @param int $staffId The ID of the employee whose work plan was modified.
     * @param string $modificationTime The date and time when the work plan was modified (e.g., "September 10th, 2024 at hh:mm:ss IST").
     * @param array $tasks An array of task objects containing details for each task.
     *                      Each task object should have the following properties:
     *                      - id (int): The ID of the task.
     *                      - name (string): The name or title of the task.
     *                      - project (string): The project reference or name.
     *                      - todoID (int): The related To-Do ID.
     *                      - status (string): The current status of the task (e.g., "Overdue", "Today", "Pending Approval").
     * @return string The HTML content for the email notifying the manager about their modification of the employee's work plan.
     */
    private function createModifiedWorkPlanByManagerEmail($mgrName, $staffName, $staffId, $modificationTime, $tasks)
    {
        // Construct the table for task details
        $taskRows = '';
        foreach ($tasks as $task) {
            $taskRows .= "
                <tr>
                    <td>" . htmlspecialchars($task['id'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['name'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['project'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['todoID'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') . "</td>
                </tr>";
        }

        // Email body content
        return "
            <p>Hi " . htmlspecialchars($mgrName, ENT_QUOTES, 'UTF-8') . ",</p>
            <p>You modified Today’s work plan of " . htmlspecialchars($staffName, ENT_QUOTES, 'UTF-8') . " (Staff ID: " . htmlspecialchars($staffId, ENT_QUOTES, 'UTF-8') . ") at " . htmlspecialchars($modificationTime, ENT_QUOTES, 'UTF-8') . ".</p>
            <p><u><strong>The updated work plan is as follows:</u></strong></p>
            <p><strong>Modified Work Plan report of:</strong> " . htmlspecialchars($staffName, ENT_QUOTES, 'UTF-8') . "</p>
            <p><strong>Modified Work Plan as on:</strong> " . htmlspecialchars($modificationTime, ENT_QUOTES, 'UTF-8') . "</p>

            <table border=\"1\" cellpadding=\"5\" cellspacing=\"0\" style=\"border-collapse: collapse; width: 100%;\">
                <thead>
                    <tr>
                       <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Task ID</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Task Name</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Ref Project</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Ref ToDoID</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Status</th>
                    </tr>
                </thead>
                <tbody>
                    " . $taskRows . "
                </tbody>
            </table>

            <p><strong>You and the staff are expected to complete all the above tasks that are self-assigned by staff or assigned by you and/or are due today. </strong></p>

            <p>Thanks,</p>
            <p>PMP Team</p>
        ";
    }

    /**
     * Create email notification for an employee when their manager modifies their work plan.
     *
     * @param string $mgrName The full name of the manager who modified the work plan.
     * @param string $employeeName The full name of the employee receiving the email.
     * @param string $modificationTime The date and time when the work plan was modified (e.g., "September 10th, 2024 at hh:mm:ss IST").
     * @param array $tasks An array of task objects containing details for each task.
     *                      Each task object should have the following properties:
     *                      - id (int): The ID of the task.
     *                      - name (string): The name or title of the task.
     *                      - project (string): The project reference or name.
     *                      - todoID (int): The related To-Do ID.
     *                      - status (string): The current status of the task (e.g., "Overdue", "Today", "Pending Approval").
     * @return string The HTML content for the email notifying the employee about their modified work plan.
     */
    private function createModifiedWorkPlanForEmployeeEmail($mgrName, $employeeName, $modificationTime, $tasks)
    {
        // Construct the table for task details
        $taskRows = '';
        foreach ($tasks as $task) {
            $taskRows .= "
                <tr>
                    <td>" . htmlspecialchars($task['id'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['name'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['project'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['todoID'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') . "</td>
                </tr>";
        }

        // Email body content
        return "
            <p>Hi " . htmlspecialchars($employeeName, ENT_QUOTES, 'UTF-8') . ",</p>
            <p>Your Manager " . htmlspecialchars($mgrName, ENT_QUOTES, 'UTF-8') . " modified your work plan today at " . htmlspecialchars($modificationTime, ENT_QUOTES, 'UTF-8') . ".</p>
            <p><u><strong>The updated work plan is as follows:</strong></u></p>
            <p><strong>Modified Work Plan report of:</strong> " . htmlspecialchars($employeeName, ENT_QUOTES, 'UTF-8') . "</p>
            <p><strong>Modified Work Plan as on:</strong> " . htmlspecialchars($modificationTime, ENT_QUOTES, 'UTF-8') . "</p>

            <table border=\"1\" cellpadding=\"5\" cellspacing=\"0\" style=\"border-collapse: collapse; width: 100%;\">
                <thead>
                    <tr>
                       <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Task ID</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Task Name</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Ref Project</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Ref ToDoID</th>
                        <th style='border: 1px solid #dddddd; text-align: left; padding: 8px;'>Status</th>
                    </tr>
                </thead>
                <tbody>
                    " . $taskRows . "
                </tbody>
            </table>

            <p><strong>You are expected to complete all the above tasks that you have self-assigned for yourself and/or are due today. </strong></p>

            <p>Thanks,</p>
            <p>PMP Team</p>
        ";
    }









    

    
    
    
}