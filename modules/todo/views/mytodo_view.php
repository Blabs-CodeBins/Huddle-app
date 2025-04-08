<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .task-checkbox {
        margin-right: 5px;
    }

    .task-item {
        display: flex;
        align-items: center;
    }

    .task-details {
        flex: 1;
    }

    .panel_s {
        margin-bottom: 20px;
        /*10px;*/
    }

    .panel-body {
        padding: 10px 10px 0px 10px;
    }

    .panel-group {
        margin-bottom: 10px;
    }

    .panel.panel-default {
        /*background-color: rgb(240, 247, 255);*/
        margin-bottom: 10px;
        box-shadow: none;
        /*border-color: rgb(186, 200, 217);*/
        padding: 10px;
    }

    .panel.panel-default.overdue-panel {
        background-color: rgb(255 240 240);
        border-color: rgb(217 186 186);
    }

    .page-header h4 {
        margin: 0;
    }

    .scrollable-container {
        max-height: 450px;
        /*264px; */
        min-height: 450px;
        overflow-y: auto;
    }

    .sticky-heading {
        position: sticky;
        top: -24px;
        background: white;
        z-index: 35;
        padding-top: 0px;
        transition: padding-top 0.3s ease;
    }



    .task_remarks {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
        font-size: 12px;
    }

    .task_view {
        text-align: right;
    }

    /* Custom scrollbar for .scrollable-container */
    .scrollable-container::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    .scrollable-container::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        -webkit-border-radius: 10px;
        border-radius: 10px;
    }

    .scrollable-container::-webkit-scrollbar-thumb {
        -webkit-border-radius: 10px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.3);
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
    }

    .scrollable-container::-webkit-scrollbar-thumb:window-inactive {
        background: rgba(255, 255, 255, 0.3);
    }

    .todo-submit-button {
        float: right;
    }

    span.help-request {
        margin: 4px 0 0;
        padding: 0px 2px;
        cursor: pointer;
    }

    .col-md-1.checkbox-sec {
        padding: 0px 0px 0px 15px;

    }

    .todocheckbox {
        cursor: pointer;
    }

    #questionsForm .form-group label,
    #answersForm .form-group label {
        margin: 2px 5px;
    }

    .text-orange {
        color: #FFA500;
    }

    .alert-warning-custom {
        --tw-bg-opacity: 1;
        --tw-text-opacity: 1;
        background-color: rgb(254 252 232/var(--tw-bg-opacity));
        border-color: hsla(53, 98%, 77%, .6);
        color: rgb(161 98 7/var(--tw-text-opacity))
    }

    form#answersForm {
        border-right: 1px solid #bfbfbf;
    }

    #huddlerTodos-needAttention ul.dropdown-menu.dropdown-menu-left {
     /* left: auto; */
        right: 0;
        min-width: 190px;
    }
</style>
<div id="wrapper">
    <div class="content" id="mytodos-content">
        <div class="row">
            <div class="col-md-offset-1 col-md-10 tw-flex tw-justify-between tw-mb-3">
                <div class="heading-title">
                    <h3 class="tw-font-semibold tw-text-gray-800 tw-m-0">Today's To Do Plan of <?php echo (isset($staffull_name)) ? $staffull_name : '[my name]'; ?></h3>
                </div>
                <div class="edit-button">
                    <a href="<?php echo admin_url(); ?>todo/EditMyTodos" class="btn btn-primary btn-sm pull-right"><i class="fa fa-pencil"></i> Edit My Plan</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-1 col-md-10 right-card">
            <div class="row">
                    <div class="col-md-6">
                        <div class="panel_s">
                            <div class="panel-body scrollable-container">
                                <div class="page-header tw-mt-0 tw-mb-0 tw-pb-1 sticky-heading">
                                    <h4>Already Overdue</h4>
                                </div>
                                <div class="panel-group tw-mt-3" id="huddlerTodos-overdue">
                                    <?php foreach ($overdueTask as $task): ?>
                                        <div class="panel panel-default overdue-panel">
                                            <div class="row tw-flex tw-justify-between">
                                                <div class="col-md-1 checkbox-sec">
                                                    <input type="checkbox" data-category="On submission" data-todoid="<?php echo $task['id']; ?>" id="todotask<?php echo $task['id']; ?>" name="todotask[]" value="<?php echo $task['task_id']; ?>" class="todocheckbox <?php echo ($task['status'] == 3) ? ' hide' : ''; ?>">
                                                    <span class="help-request <?php echo ($task['pm_remarks'] != '' || $task['status'] == 3) ? ' hide' : ''; ?>"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
                                                </div>
                                                <div class="col-md-11 content-sec">
                                                    <div class="row">
                                                        <div class="col-md-12 task-name">
                                                            <div class="task-name-status">
                                                                <a href="<?php echo admin_url(); ?>/tasks/view/<?php echo $task['task_id']; ?>" onclick="init_task_modal(<?php echo $task['task_id']; ?>);return false;">
                                                                    <span class="inline-block full-width tw-truncate">#<?php echo $task['task_id']; ?> - <?php echo $task['task_name']; ?></span>
                                                                </a>
                                                            </div>
                                                            <?php if (isset($task['rel_id']) && $task['rel_id'] != NULL) { ?>
                                                                <a class="tw-text-neutral-600 tw-truncate -tw-mt-1 tw-block tw-text-sm" data-toggle="tooltip" title="" href="<?php echo admin_url(); ?>/projects/view/<?php echo $task['rel_id']; ?>" data-original-title="Related To">#<?php echo $task['rel_id']; ?> - <?php echo $task['project_name']; ?></a>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="col-md-4 tw-text-neutral-600 mtop10 tw-text-sm">
                                                            <div class="tw-flex -tw-space-x-1">
                                                                <a href="<?php echo admin_url(); ?>profile/<?= $task['staff_id'] ?>">
                                                                    <?php echo staff_profile_image($task['staff_id'], ['img', 'img-responsive', 'staff-profile-image-small', 'tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white'],'small', ['data-toggle' => 'tooltip', 'data-title'  => get_staff_full_name($task['staff_id']),]); ?>
                                                                    <!-- <img data-toggle="tooltip" data-title="<?= $staffull_name ?>" src="http://localhost/PmpHuddler/pmpdev2/uploads/staff_profile_images/<?= $staffid ?>/small_<?= $staffull_profile ?>" class="tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white"> -->
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8 text-right tw-text-neutral-500 mtop10 tw-text-sm">
                                                            <?php
                                                            // Determine the icon and color based on task status
                                                            switch ($task['status']) {
                                                                case 1:
                                                                    $icon = 'fas fa-user-times';
                                                                    $color = 'text-muted';
                                                                    $status_label = 'Not Assigned';
                                                                    break;
                                                                case 2:
                                                                    $icon = 'fas fa-user-check';
                                                                    $color = 'text-info';
                                                                    $status_label = 'Assigned';
                                                                    break;
                                                                case 3:
                                                                    $icon = 'fas fa-clock';
                                                                    $color = 'text-orange';
                                                                    $status_label = 'Approval Pending';
                                                                    break;
                                                                case 4:
                                                                    $icon = 'fas fa-thumbs-up';
                                                                    $color = 'text-primary';
                                                                    $status_label = 'PM Approved';
                                                                    break;
                                                                case 5:
                                                                    $icon = 'fas fa-user-edit';
                                                                    $color = 'text-danger';
                                                                    $status_label = 'Re Assigned';
                                                                    break;
                                                                case 6:
                                                                    $icon = 'fas fa-hands-helping';
                                                                    $color = 'text-warning';
                                                                    $status_label = 'Help Request';
                                                                    break;
                                                                case 7:
                                                                    $icon = 'fas fa-exclamation-triangle';
                                                                    $color = 'text-danger';
                                                                    $status_label = 'Followup to complete';
                                                                    break;
                                                                default:
                                                                    $icon = 'fas fa-question-circle';
                                                                    $color = 'text-muted';
                                                                    $status_label = 'Unknown';
                                                            }
                                                            ?>
                                                            <span class="<?php echo $color; ?>" data-toggle="tooltip" title="" data-original-title="Status">
                                                                <i class="<?php echo $icon; ?>"></i> <?php echo $status_label; ?>
                                                            </span>
                                                            &nbsp;
                                                            <span class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Start Date">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                <?php echo date('d-m-Y', strtotime($task['start_date'])); ?>
                                                            </span>
                                                            &nbsp;
                                                            <span class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Due Date">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                <?php echo date('d-m-Y', strtotime($task['due_date'])); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if(count($overdueTask) == 0): ?>
                                        <div class="col-md-12">
                                            <div class="alert alert-info d-flex align-items-center tw-p-1 tw-mb-0" role="alert">
                                                <i class="glyphicon glyphicon-info-sign" style="margin-right: 8px;"></i>
                                                <span>Great job! No overdue tasks found. Keep up the good work!</span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel_s">
                            <div class="panel-body scrollable-container" id="myScrollableContainer">
                                <div class="page-header tw-mt-0 tw-mb-0 tw-pb-1 sticky-heading">
                                    <h4>Today's To-Do</h4>
                                </div>
                                <div class="panel-group tw-mt-3" id="huddlerTodos-todaysTodo">
                                    <?php foreach ($todaysToDo as $task): ?>
                                        <div class="panel panel-default">
                                            <div class="row tw-flex tw-justify-between">
                                                <div class="col-md-1 checkbox-sec">
                                                    <input type="checkbox" data-category="On submission" data-todoid="<?php echo $task['id']; ?>" id="todotask<?php echo $task['id']; ?>" name="todotask[]" value="<?php echo $task['task_id']; ?>" class="todocheckbox <?php echo ($task['status'] == 3) ? ' hide' : ''; ?>">
                                                    <span class="help-request <?php echo ($task['pm_remarks'] != '' || $task['status'] == 6 || $task['status'] == 3) ? ' hide' : ''; ?>"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
                                                </div>
                                                <div class="col-md-11 content-sec">
                                                    <div class="row">
                                                        <div class="col-md-12 task-name">
                                                            <div class="task-name-status">
                                                                <a href="<?php echo admin_url(); ?>/tasks/view/<?php echo $task['task_id']; ?>" onclick="init_task_modal(<?php echo $task['task_id']; ?>);return false;">
                                                                    <span class="inline-block full-width tw-truncate">#<?php echo $task['task_id']; ?> - <?php echo $task['task_name']; ?></span>
                                                                </a>
                                                            </div>
                                                            <?php if (isset($task['rel_id']) && $task['rel_id'] != NULL) { ?>
                                                                <a class="tw-text-neutral-600 tw-truncate -tw-mt-1 tw-block tw-text-sm" data-toggle="tooltip" title="" href="<?php echo admin_url(); ?>/projects/view/<?php echo $task['rel_id']; ?>" data-original-title="Related To">#<?php echo $task['rel_id']; ?> - <?php echo $task['project_name']; ?></a>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="col-md-4 tw-text-neutral-600 mtop10 tw-text-sm">
                                                            <div class="tw-flex -tw-space-x-1">
                                                                <a href="<?php echo admin_url(); ?>profile/<?= $task['staff_id'] ?>">
                                                                    <?php echo staff_profile_image($task['staff_id'], ['img', 'img-responsive', 'staff-profile-image-small', 'tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white'],'small', ['data-toggle' => 'tooltip', 'data-title'  => get_staff_full_name($task['staff_id']),]); ?>
                                                                    <!-- <img data-toggle="tooltip" data-title="<?= $staffull_name ?>" src="http://localhost/PmpHuddler/pmpdev2/uploads/staff_profile_images/<?= $staffid ?>/small_<?= $staffull_profile ?>" class="tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white"> -->
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8 text-right tw-text-neutral-500 mtop10 tw-text-sm">
                                                            <?php
                                                            // Determine the icon and color based on task status
                                                            switch ($task['status']) {
                                                                case 1:
                                                                    $icon = 'fas fa-user-times';
                                                                    $color = 'text-muted';
                                                                    $status_label = 'Not Assigned';
                                                                    break;
                                                                case 2:
                                                                    $icon = 'fas fa-user-check';
                                                                    $color = 'text-info';
                                                                    $status_label = 'Assigned';
                                                                    break;
                                                                case 3:
                                                                    $icon = 'fas fa-clock';
                                                                    $color = 'text-orange';
                                                                    $status_label = 'Approval Pending';
                                                                    break;
                                                                case 4:
                                                                    $icon = 'fas fa-thumbs-up';
                                                                    $color = 'text-primary';
                                                                    $status_label = 'PM Approved';
                                                                    break;
                                                                case 5:
                                                                    $icon = 'fas fa-user-edit';
                                                                    $color = 'text-danger';
                                                                    $status_label = 'Re Assigned';
                                                                    break;
                                                                case 6:
                                                                    $icon = 'fas fa-hands-helping';
                                                                    $color = 'text-warning';
                                                                    $status_label = 'Help Request';
                                                                    break;
                                                                case 7:
                                                                    $icon = 'fas fa-exclamation-triangle';
                                                                    $color = 'text-danger';
                                                                    $status_label = 'Followup to complete';
                                                                    break;
                                                                default:
                                                                    $icon = 'fas fa-question-circle';
                                                                    $color = 'text-muted';
                                                                    $status_label = 'Unknown';
                                                            }
                                                            ?>
                                                            <span class="<?php echo $color; ?>" data-toggle="tooltip" title="" data-original-title="Status">
                                                                <i class="<?php echo $icon; ?>"></i> <?php echo $status_label; ?>
                                                            </span>
                                                            &nbsp;
                                                            <span class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Start Date">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                <?php echo date('d-m-Y', strtotime($task['start_date'])); ?>
                                                            </span>
                                                            &nbsp;
                                                            <span class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Due Date">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                <?php echo date('d-m-Y', strtotime($task['due_date'])); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if(count($todaysToDo) == 0): ?>
                                        <div class="col-md-12">
                                            <div class="alert alert-warning d-flex align-items-center tw-p-1 tw-mb-0" role="alert">
                                                <i class="glyphicon glyphicon-info-sign" style="margin-right: 8px;"></i>
                                                <span>No tasks listed in your To-Do for today. Request tasks from your PM/TL if needed.</span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel_s">
                            <div class="panel-body scrollable-container">
                                <div class="page-header tw-mt-0 tw-mb-0 tw-pb-1 sticky-heading">
                                    <h4>Needs Attention</h4>
                                </div>
                                <div class="panel-group tw-mt-3" id="huddlerTodos-needAttention">
                                    <?php foreach ($needsAttention as $task): ?>
                                        <div class="panel panel-default">
                                            <div class="row tw-flex ">
                                                <div class="col-md-1 checkbox-sec">
                                                    <input type="checkbox" data-category="On submission" data-todoid="<?php echo $task['id']; ?>" id="todotask<?php echo $task['id']; ?>" name="todotask[]" value="<?php echo $task['task_id']; ?>" class="todocheckbox <?php echo ($task['status'] == 3) ? ' hide' : ''; ?>">
                                                    <span class="help-request"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
                                                </div>
                                                <div class="col-md-11 content-sec">
                                                    <div class="row">
                                                        <div class="task-name tw-pl-2 tw-pr-2 col-sm-8">
                                                            <a href="<?php echo admin_url(); ?>/tasks/view/<?php echo $task['task_id']; ?>" onclick="init_task_modal(<?php echo $task['task_id']; ?>);return false;">
                                                                <span class="inline-block full-width tw-truncate">#<?php echo $task['task_id']; ?> - <?php echo $task['task_name']; ?></span>
                                                            </a>
                                                        </div>
                                                        <div class="task-action col-lg-1 col-sm-4 !tw-p-0 ">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm dropdown-toggle actionMenu" type="button" id="actionMenu<?php echo $task['task_id']; ?>" data-toggle="dropdown" aria-expanded="true" style="color:#3b82f6;border:1px solid #b1cdfb;background: #f7faff;">
                                                                    Action <span class="caret"></span>
                                                                </button>
                                                                <?php
                                                                $statuses = [
                                                                    'Bug Found' => 2,
                                                                    'New Work' => 3,
                                                                    'No Problem' => 4
                                                                ];

                                                                // Ensure 'Mark as Complete' is added only for authorized users
                                                                if ($isProjectManager || $istoplinemanager || is_admin($staffid)) {
                                                                    $statuses = ['Mark as Complete' => 1] + $statuses;
                                                                }

                                                                $currentStatus = $task['needs_attention_status'];
                                                                echo '<ul class="dropdown-menu dropdown-menu-left" aria-labelledby="actionMenu' . $task['task_id'] . '">';
                                                                foreach ($statuses as $status => $actionCode) {
                                                                    if ($currentStatus != $status) { // Corrected the condition to check against the action code
                                                                        echo '<li><a href="#" onclick="huddler_mark_as(' . $actionCode . ',' . $task['task_id'] . ',' . $task['id'] . '); return false;">' . $status . '</a></li>';
                                                                    }
                                                                }
                                                                echo '</ul>';
                                                                ?>

                                                            </div>
                                                        </div>
                                                        <div class="project-name tw-pl-2 tw-pr-2 tw-mb-2 col-sm-8">
                                                            <?php if (isset($task['rel_id']) && $task['rel_id'] != NULL) { ?>
                                                                <a class="tw-text-neutral-600 tw-truncate -tw-mt-1 tw-block tw-text-sm" data-toggle="tooltip" title="" href="<?php echo admin_url(); ?>/projects/view/<?php echo $task['rel_id']; ?>" data-original-title="Related To">#<?php echo $task['rel_id']; ?> - <?php echo $task['project_name']; ?></a>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="user-profile col-lg-2 col-sm-3 tw-pl-2 tw-pr-2 tw-mb-2">
                                                            <div class="tw-flex -tw-space-x-1">
                                                                <a href="<?php echo admin_url(); ?>profile/<?= $task['staff_id'] ?>">
                                                                    <?php echo staff_profile_image($task['staff_id'], ['img', 'img-responsive', 'staff-profile-image-small', 'tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white'], 'small', ['data-toggle' => 'tooltip', 'data-title'  => get_staff_full_name($task['staff_id']),]); ?>
                                                                    <!-- <img data-toggle="tooltip" data-title="<?= get_staff_full_name($task['staff_id']) ?>" src="http://localhost/PmpHuddler/pmpdev2/uploads/staff_profile_images/<?= $staffid ?>/small_<?= $staffull_profile ?>" class="tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white"> -->
                                                                </a>
                                                                <?php if ($istoplinemanager || is_admin()) { ?>
                                                                    <a href="#"
                                                                        data-todo-id="<?php echo $task['id']; ?>"
                                                                        data-task-id="<?php echo $task['task_id']; ?>"
                                                                        data-target="#add-edit-members"
                                                                        data-toggle="modal"
                                                                        class="tw-mt-1.5 rtl:tw-mr-3 open-modal">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                            stroke-width="1.5" stroke="currentColor" class="tw-w-5 tw-h-5">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                                                        </svg>
                                                                    </a>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <div class="task-status col-lg-4 col-sm-9  tw-mt-2 !tw-p-0">
                                                            <?php
                                                            // Determine the icon and color based on task status
                                                            switch ($task['status']) {
                                                                case 1:
                                                                    $icon = 'fas fa-user-times';
                                                                    $color = 'text-muted';
                                                                    $status_label = 'Not Assigned';
                                                                    break;
                                                                case 2:
                                                                    $icon = 'fas fa-user-check';
                                                                    $color = 'text-info';
                                                                    $status_label = 'Assigned';
                                                                    break;
                                                                case 3:
                                                                    $icon = 'fas fa-clock';
                                                                    $color = 'text-orange';
                                                                    $status_label = 'Approval Pending';
                                                                    break;
                                                                case 4:
                                                                    $icon = 'fas fa-thumbs-up';
                                                                    $color = 'text-primary';
                                                                    $status_label = 'PM Approved';
                                                                    break;
                                                                case 5:
                                                                    $icon = 'fas fa-user-edit';
                                                                    $color = 'text-danger';
                                                                    $status_label = 'Re Assigned';
                                                                    break;
                                                                case 6:
                                                                    $icon = 'fas fa-hands-helping';
                                                                    $color = 'text-warning';
                                                                    $status_label = 'Help Request';
                                                                    break;
                                                                case 7:
                                                                    $icon = 'fas fa-exclamation-triangle';
                                                                    $color = 'text-danger';
                                                                    $status_label = 'Followup to complete';
                                                                    break;
                                                                default:
                                                                    $icon = 'fas fa-question-circle';
                                                                    $color = 'text-muted';
                                                                    $status_label = 'Unknown';
                                                            }
                                                            ?>
                                                            <span class="<?php echo $color; ?>" data-toggle="tooltip" title="" data-original-title="Status">
                                                                <i class="<?php echo $icon; ?>"></i> <?php echo ($task['new_comment_by_client'] == 1) ? 'New comment by client' : $status_label; ?>
                                                            </span>
                                                        </div>
                                                        <div class="task-date col-lg-5 col-sm-10 tw-text-sm  tw-mt-2 !tw-p-0">
                                                            <span class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Start Date">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                <?php echo date('d-m-Y', strtotime($task['start_date'])); ?>
                                                            </span>
                                                            &nbsp;
                                                            <span class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Due Date">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                <?php echo date('d-m-Y', strtotime($task['due_date'])); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if(count($needsAttention) == 0): ?>
                                        <div class="col-md-12">
                                            <div class="alert alert-warning d-flex align-items-center tw-p-1 tw-mb-0" role="alert">
                                                <i class="glyphicon glyphicon-info-sign" style="margin-right: 8px;"></i>
                                                <span>No tasks need immediate attention.</span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <!-- Questions Modal -->
         <div class="modal fade" id="questionsModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title col-md-10">Questions for Task</h4>
                        <!-- <button type="button" class="col-md-2 close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button> -->
                    </div>
                    <div class="modal-body row">
                        <div class="row" id="heading-toapprove" style="display: none;">
                            <div class="col-md-6">
                                <h4 class="bold tw-ml-4">Submission form view</h4>
                            </div>
                            <div class="col-md-6">
                                <h4 class="bold">Your Approval Panel</h4>
                            </div>
                        </div>
                        <form class="col-md-6" id="answersForm" style="display: none;">
                            <div class="form-group">
                                <label>Is the task on schedule?</label>
                                <div>
                                    <label><input type="radio" name="on_schedule" value="yes"> Yes</label>
                                    <label><input type="radio" name="on_schedule" value="no"> No</label>
                                </div>
                            </div>
                            <!-- Add more questions here -->
                            <input type="hidden" name="task_id" id="questionsTaskId">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                        <form id="questionsForm">
                            <div class="form-group">
                                <label>Is the task on schedule?</label>
                                <div>
                                    <label><input type="radio" name="on_schedule" value="yes"> Yes</label>
                                    <label><input type="radio" name="on_schedule" value="no"> No</label>
                                </div>
                            </div>
                            <!-- Add more questions here -->
                            <input type="hidden" name="task_id" id="questionsTaskId">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Help Request Modal for Employees -->
        <div class="modal fade" id="helpRequestModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-head-title col-md-10">
                            <h4 class="help-title">Help Request for:</h4>                              
                        </div>
                        <button type="button" class="close col-md-2" data-dismiss="modal" aria-label="Close">
                            <span class="bold" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php echo form_open(admin_url('todo/createHelpRequest'), ['id' => 'helpRequestForm']); ?>
                        <div class="form-group">
                            <label for="problemDescription">Describe your problem</label>
                            <textarea class="form-control" id="problemDescription" name="problem_description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="loomLink">Loom Video/Screenshot Link (Optional)</label>
                            <input type="url" class="form-control" id="loomLink" name="loom_link" placeholder="https://www.loom.com/">
                        </div>
                        <input type="hidden" name="task_id" id="helpRequestTaskId">
                        <button type="submit" class="btn btn-primary" data-dismiss="modal">Submit Help Request</button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
<script>
    $(document).ready(function() {
        $('.scrollable-container').each(function() {
            var scrollableContainer = $(this);
            var stickyHeading = scrollableContainer.find('.sticky-heading');

            scrollableContainer.on('scroll', function() {
                if (scrollableContainer.scrollTop() > 0) {
                    stickyHeading.css('padding-top', '18px');
                } else {
                    stickyHeading.css('padding-top', '0px');
                }
            });
        });
        $(document).on('click', '.help-request', function() {
            var taskId = $(this).closest('.panel').find('input[name="todotask[]"]').val();
            var todoId = $(this).closest('.panel').find('input[name="todotask[]"]').data('todoid');

            // Get CSRF token from the form
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
            var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

            $.ajax({
                type: "POST",
                url: "<?php echo admin_url(); ?>todo/getQuestions",
                data: {
                    task_ids: taskId,
                    category: 'Help Request',
                    status: 1
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Update modal title with task ID and name
                        $('#helpRequestModal .modal-head-title').empty();
                        $('#helpRequestModal .modal-head-title').append('<h4 class="help-title">Help Request for:</h4>');
                        $('#helpRequestModal .modal-head-title').append('<a href="<?php echo admin_url(); ?>tasks/view/' + response.tasks.id + '" target="_blank" class="inline-block full-width tw-truncate">Task #' + response.tasks.id + ' - ' + response.tasks.name + '</a>');
                        $('#helpRequestModal .modal-head-title').append('<a href="<?php echo admin_url(); ?>projects/view/' + response.tasks.project_data.id + '" target="_blank" class="tw-text-neutral-600 inline-block full-width tw-truncate">PID #' + response.tasks.project_data.id + ' - ' + response.tasks.project_data.name + '</a>');

                        // Clear previous form fields
                        $('#helpRequestForm').empty();

                        // Iterate over questions and dynamically create form fields
                        $.each(response.questions, function(index, question) {
                            var formGroup = $('<div class="form-group"></div>');
                            formGroup.append('<label>' + question.question_text + '</label>');

                            if (question.options === '[description box]') {
                                formGroup.append('<textarea class="form-control" name="emp_remarks" rows="10"></textarea>');
                            } else if (question.options === '[url]') {
                                formGroup.append('<input type="url" class="form-control" name="video_link" placeholder="https://www.loom.com/">');
                            } else if (question.options === 'Yes / No') {
                                formGroup.append('<div><label><input type="radio" name="question_' + question.id + '" value="yes"> Yes</label> <label><input type="radio" name="question_' + question.id + '" value="no"> No</label></div>');
                            }

                            $('#helpRequestForm').append(formGroup);
                        });

                        // Add hidden fields for todoId, taskId, and requested_by
                        $('#helpRequestForm').append('<input type="hidden" name="todoid" value="' + todoId + '">');
                        $('#helpRequestForm').append('<input type="hidden" name="taskid" value="' + response.tasks.id + '">');
                        $('#helpRequestForm').append('<input type="hidden" name="requested_by" value="' + <?php echo $staffid; ?> + '">');
                        $('#helpRequestForm').append('<input type="hidden" name="' + csrfName + '" value="' + csrfHash + '">');
                        $('#helpRequestForm').append('<button type="submit" class="btn btn-primary">Submit Help Request</button>');

                        // Show the modal
                        $('#helpRequestModal').modal('show');
                    }
                },
                error: function(error) {
                    alert('An error occurred while creating Today\'s Plan.');
                }
            });

            // Set the task ID in the hidden input field
            $('#helpRequestTaskId').val(taskId);
        });
    });
    $(document).ready(function() {
        // Handle change event for checkboxes
        $(document).on('change', 'input[name="todotask[]"]', function() {
            if ($(this).is(':checked')) {
                var taskId = $(this).val();
                var todoId = $(this).data('todoid');
                var hrId = $(this).data('hrid');
                var QuestionCategory = $(this).data('category');
                getQuestions(taskId, todoId, hrId, QuestionCategory);
            }
        });
        $('.modal-header').on('click', '.close', function(e) {
            var checkboxid = $(this).data('checkboxid');
            $('input[name="todotask[]"').prop('checked', false);
        });
        // Handle change event for radio buttons
        $('#questionsForm').on('change', 'input[type="radio"]', function() {
            var selectedValue = $(this).val();
            var formGroup = $(this).closest('.form-group');
            var warningMessage = formGroup.find('.alert-warning');
            var optionsWarning = formGroup.find('.question_text').data('optionswarning');

            if (selectedValue === optionsWarning) {
                warningMessage.show();
            } else {
                warningMessage.hide();
            }
        });
        $('#questionsForm').on('change', 'input[type="radio"], input[name="question_7"]', function() {
            //e.preventDefault();
            checkFormValidity();
        });
        // Handle form submission
        $('#questionsForm').submit(function(e) {
            e.preventDefault();
            $(e.originalEvent.submitter).prop('disabled', true);

            checkFormValidity();
            var submitButtonId = $(e.originalEvent.submitter).attr('id');
            console.log(submitButtonId);
            var isValid = !$('#questionsForm .val-error').length;
            if (submitButtonId === 'Submitbtn' && isValid) {
                $('#questionsModal .modal-content').append('<div class="dt-loader"></div>');
                var formData = $(this).serialize();
                var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
                var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
                var todoId = $(this).find('input[name="todoid"]').val();

                // Add CSRF token to the serialized data
                formData += '&' + csrfName + '=' + csrfHash;

                $.ajax({
                    type: "POST",
                    url: "<?php echo admin_url(); ?>todo/submitQuestions",
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#questionsModal .modal-content').find(".dt-loader").remove();
                            $(e.originalEvent.submitter).prop('disabled', false);
                            alert_float(response.taskStatus, response.message);
                            $('#questionsModal').modal('hide');
                            $('#todotask' + todoId).hide();
                            setTimeout(function() {
                                window.location.reload();
                            }, 3000);
                        } else {
                            $(e.originalEvent.submitter).prop('disabled', false);
                            alert_float("danger", 'An error occurred while submitting the questions.');
                        }
                    },
                    error: function() {
                        $(e.originalEvent.submitter).prop('disabled', false);
                        alert_float("danger", 'An error occurred while submitting the questions.');
                    }
                });
            }
        });
        var originalHiddenFormGroupHtml;

        $('#questionsForm').on('change', 'input[name="question_12"]', function() {
            var selectedValue = $(this).val();

            if (!originalHiddenFormGroupHtml) {
                // Store the original hidden form group HTML only once
                originalHiddenFormGroupHtml = $('#questionsForm .form-group.hidden').html();
            }
            if (selectedValue === "No") {
                $('#gotoTask').show();
                $('#bugfoundComments').hide();
                $('#questionsForm .form-group.hidden').removeClass('hidden');
                $('#questionsForm .form-group').last().html(originalHiddenFormGroupHtml);
                $('#questionsForm button').text('Approved');
            } else {
                $('#gotoTask').hide();
                $('#bugfoundComments').show();
                $('#questionsForm .form-group').last().html(' '); // Clear the content
                $('#questionsForm .form-group').last().addClass('hidden');
                $('#questionsForm button').text('Rejected');
            }

            checkFormValidity();
        });
        $('#questionsForm').on('change', 'input[name="question_14"]', function() {
            var selectedValue = $(this).val();
            if (selectedValue === "No") {
                $('#gotoTask').show();
            } else {
                $('#gotoTask').hide();
            }

            checkFormValidity();
        });
        $('.dropdown-menu li a').click(function() {
            var selectedText = $(this).text();
            var actionButton = $(this).closest('.dropdown').find('.actionMenu');
            actionButton.html(selectedText + ' <span class="caret"></span>');
        });
    });
    // Function to validate the form
    // function checkFormValidity() {
    //     var isValid = true;
    //     var $lastFormGroup = $('#questionsForm .form-group').not('.hidden').last();
    //     $('#questionsForm .val-error').remove();
    //     $('#questionsForm .form-group').not('.hidden').each(function() {
    //         var $group = $(this);
    //         var isQuestionAnswered = $group.find('input[type="radio"]:checked').length > 0;
    //         if (!isQuestionAnswered && $group.find('input[type="radio"]').length > 0) {
    //             isValid = false;
    //             $lastFormGroup.append('<div class="text-danger tw-mt-1 val-error-all val-error">All Fields are required.</div>');
    //             return false;
    //         }
    //     });
    //     var loomUrl = $('input[name="question_7"]').val();
    //     // if (loomUrl !== undefined) {
    //     //     if (!loomUrl.match(/^https:\/\/www\.loom\.com\/.+/)) {
    //     //         isValid = false;
    //     //         $lastFormGroup.append('<div class="text-danger tw-mt-1 val-error-video val-error">Please enter a valid Loom video URL.</div>');
    //     //     }
    //     // }
    //     if (loomUrl !== undefined && loomUrl !== '') {
    //         const isLoomVideo = /^https:\/\/www\.loom\.com\/.+/.test(loomUrl);
    //         const isLightshotScreenshot = /^https:\/\/prnt\.sc\/.+/.test(loomUrl);

    //         if (!isLoomVideo && !isLightshotScreenshot) {
    //             isValid = false;
    //             $lastFormGroup.append('<div class="text-danger tw-mt-1 val-error-video val-error">Please enter a valid Loom video or Lightshot screenshot URL.</div>');
    //         }
    //     }
    //     $('#questionsForm .form-group').not('.hidden').each(function() {
    //         var $group = $(this);
    //         // Skip validation for the radio input with name "question_5"
    //         if ($group.find('input[type="radio"][name="question_5"]').length > 0) {
    //             return true;
    //         }
    //         var optionsWarning = $group.find('.question_text').data('optionswarning');

    //         if (optionsWarning !== undefined) {
    //             var selectedValue = $group.find('input[type="radio"]:checked').val();
    //             var warningMessage = $group.find('.alert-warning');

    //             if (selectedValue === optionsWarning && warningMessage.is(':visible')) {
    //                 isValid = false;
    //                 return false;
    //             }
    //         }
    //     });
    //     // Disable submit button if form is not valid or warnings are visible
    //     $('#questionsForm button[type="submit"]').prop('disabled', !isValid);
    // }
    function checkFormValidity() {
        var isValid = true;
        var $form = $('#questionsForm');
        var $visibleGroups = $form.find('.form-group').not('.hidden');
        var $lastFormGroup = $visibleGroups.last();

        // Remove previous error messages
        $form.find('.val-error').remove();

        // Validate radio button questions
        $visibleGroups.each(function () {
            var $group = $(this);

            var hasRadios = $group.find('input[type="radio"]').length > 0;
            var isAnswered = $group.find('input[type="radio"]:checked').length > 0;

            if (hasRadios && !isAnswered) {
                isValid = false;
                showError($lastFormGroup, 'All Fields are required.', 'val-error-all');
                return false; // Break loop on first invalid group
            }
        });

        // Validate Loom or Lightshot URL (question_7)
        var loomUrl = $('input[name="question_7"]').val()?.trim();

        if (loomUrl) {
            var isLoomVideo = /^https:\/\/www\.loom\.com\/.+/.test(loomUrl);
            var isLightshotScreenshot = /^https:\/\/prnt\.sc\/.+/.test(loomUrl);

            if (!isLoomVideo && !isLightshotScreenshot) {
                isValid = false;
                showError($lastFormGroup, 'Please enter a valid Loom video or Lightshot screenshot URL.', 'val-error-video');
            }
        }

        // Validate options warning (except for question_5)
        $visibleGroups.each(function () {
            var $group = $(this);
            var questionName = $group.find('input[type="radio"]').attr('name');

            // Skip question_5 validation
            if (questionName === 'question_5') {
                return true; // Continue loop
            }

            var optionsWarning = $group.find('.question_text').data('optionswarning');
            if (optionsWarning !== undefined) {
                var selectedValue = $group.find('input[type="radio"]:checked').val();
                var warningMessage = $group.find('.alert-warning');

                if (selectedValue === optionsWarning && warningMessage.is(':visible')) {
                    isValid = false;
                    return false; // Break loop on first invalid warning
                }
            }
        });

        // Debug logs (optional - can remove in production)
        //console.log('Form validity:', isValid);

        // Enable/Disable submit button based on validity
        $form.find('button[type="submit"]').prop('disabled', !isValid);

        // Reusable function to show error messages
        function showError($targetGroup, message, errorClass) {
            var errorHtml = '<div class="text-danger tw-mt-1 val-error ' + errorClass + '">' + message + '</div>';
            $targetGroup.append(errorHtml);
        }
    }

    function huddler_mark_as($conditon, $taskId, $todoId) {

        let confirmMessage = "";
        if ($conditon === 1) {
            confirmMessage = "Are you sure you want to mark this task as Complete?";
        } else if ($conditon === 2) {
            confirmMessage = "Are you sure you want to report this task as a Bug?";
        } else if ($conditon === 3) {
            confirmMessage = "Are you sure you want to submit a New Work query?";
        } else if ($conditon === 4) {
            confirmMessage = "Are you sure you want to submit a No Problem query?";
        }
        var r = confirm(confirmMessage); 
        if (!r) {
            return false; 
        }

        if ($conditon === 1) {
            task_mark_as(5, $taskId); // marked the respective task as completed
            setTimeout(function() {
                window.location.reload();
            }, 3000);
        }
        if ($conditon === 2) {
            var ajaxurl = '<?php echo admin_url(); ?>/todo/submitQuestions';
            var data = {
                taskid: $taskId,
                todoid: $todoId,
                category: 'Bug Found',
                submited_by: <?php echo $staffid; ?>
            };
            $.post(ajaxurl, data, response => alert_float(response.taskStatus, response.message), 'json');
            setTimeout(function() {
                window.location.reload();
            }, 3000);
        }
        if ($conditon === 3) {
            $('#questionsModal').modal('show');
            getQuestions($taskId, $todoId, '', 'New Work');
        }
        if ($conditon === 4) {
            $('#questionsModal').modal('show');

            getQuestions($taskId, $todoId, '', 'No Problem');
            $('#questionsForm').on('click', '#confirmbtn', function(e) {
                task_mark_as(5, $taskId); // marked the respective task as completed
                $('#questionsModal').modal('hide');
                setTimeout(function() {
                    window.location.reload();
                }, 3000);
            });

        }



    }
    
    function getQuestions(taskId, todoId, hrId, QuestionCategory) {
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
        var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
            type: "POST",
            url: "<?php echo admin_url(); ?>todo/getQuestions",
            data: {
                task_ids: taskId,
                hrId: hrId,
                category: QuestionCategory,
                status: 1
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Update modal title with task ID and name
                    $('#questionsModal .modal-title').text('Questions for Task #' + response.tasks.id + ' - ' + response.tasks.name);
                    var closeBtn = '<button type="button" class="col-md-2 close" data-checkboxid="todotask' + todoId + '" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span></button>';
                    if (response.questionAnswer && response.questionAnswer.length > 0) {
                        $('#questionsModal .modal-dialog').removeClass('modal-lg');
                        $('#questionsModal .modal-dialog').addClass('modal-xl');
                        $('#questionsForm').addClass('col-md-6');
                        $('#questionsForm').removeClass('col-md-12');
                        $('#answersForm').show();
                        $('#heading-toapprove').show();
                        $('button.close').remove();
                    } else {
                        $('#questionsForm').removeClass('col-md-6');
                        $('#questionsForm').addClass('col-md-12');
                        $('#answersForm').hide();
                        $('#heading-toapprove').hide();
                        $('#questionsModal .modal-dialog').removeClass('modal-xl');
                        $('#questionsModal .modal-dialog').addClass('modal-lg');
                        $('button.close').remove();
                        $(closeBtn).appendTo('.modal-header');
                    }
                    // Clear previous form fields
                    $('#questionsForm').empty();
                    $('#answersForm').empty();
                    var buttonType = '';
                    // Iterate over questions and dynamically create form fields
                    $.each(response.questions, function(index, question) {
                        if (question.id == 13) {
                            $hideClass = ' hidden';
                        } else {
                            $hideClass = '';
                        }
                        var formGroup = $('<div class="form-group' + $hideClass + '"></div>');
                        formGroup.append('<label class="question_text"' + (question.opposite_warning_trigger ? ' data-optionswarning="' + question.opposite_warning_trigger + '"' : '') + '>' + question.question_text + '</label>');


                        if (question.options === '[description box]') {
                            formGroup.append('<textarea class="form-control" name="question_' + question.id + '" rows="3"></textarea>');
                        } else if (question.options === '[url]') {
                            // formGroup.append('<input type="url" class="form-control" name="question_' + question.id + '" placeholder="https://www.loom.com/"><div class="text-danger tw-mt-1">' + question.opposite_warning + '</div>');
                            formGroup.append('<input type="url" class="form-control" name="question_' + question.id + '" placeholder="https://www.loom.com/">' + (question.opposite_warning ? '<div class="text-danger tw-mt-1">' + question.opposite_warning + '</div>' : ''));

                        } else if (question.options === 'Yes / No') {
                            formGroup.append('<div class="tw-flex tw-items-center"><label><input type="radio" name="question_' + question.id + '" value="Yes"> Yes</label> <label><input type="radio" name="question_' + question.id + '" value="No"> No</label><div class="alert alert-warning tw-ml-2 tw-p-1 tw-mb-0 col-md-9" style="display: none;">' + question.opposite_warning + '</div></div>');

                            if (question.id == 12) {
                                formGroup.append('<textarea class="form-control" id="bugfoundComments" placeholder="Provide a brief explanation of the bugs" name="bugfoundComments' + question.id + '" rows="3" style="display:none;"></textarea>');
                            }
                            if (question.id == 14 || question.id == 12) {

                                formGroup.append('<div class="tw-mt-3" id="gotoTask" style="display:none;"><label>Go to the respective task and put your comments.</label><p class="tw-ml-2">Link to task: <a href="<?php echo admin_url(); ?>/tasks/view/' + response.tasks.id + '" target="_blank"> #' + response.tasks.id + ' - ' + response.tasks.name + '</a> </p></div>');
                            }

                        }
                        if (question.id == 10) {
                            formGroup.append('<div class="alert alert-warning-custom tw-mt-2 tw-p-1">Check the task thoroughly. Your approval on this task is final, and no other bugs of any kind will be expected. One client complain or bug report leads to 2x reduction in points.</div>');
                        }

                        buttonType = (question.id == 16 ? 'confirmbtn' : 'Submitbtn');

                        $('#questionsForm').append(formGroup);
                    });
                    $.each(response.questionAnswer, function(index, question) {
                        var formGroup = $('<div class="form-group"></div>');
                        formGroup.append('<label class="question_text">' + question.question_text + '</label>');

                        if (question.options === '[description box]') {
                            formGroup.append('<textarea class="form-control" name="question_' + question.id + '" rows="3" disabled>' + question.answer + '</textarea>');
                        } else if (question.options === '[url]') {
                            formGroup.append('<a href="' + question.answer + '" target="_blank"> ' + question.answer + '</a>');
                        } else if (question.options === 'Yes / No') {
                            var selectedYes = question.answer === 'Yes' ? 'checked' : '';
                            var selectedNo = question.answer === 'No' ? 'checked' : '';
                            formGroup.append('<div class="tw-flex tw-items-center"><label><input type="radio" name="question_' + question.id + '" value="Yes" ' + selectedYes + ' disabled> Yes</label> <label><input type="radio" name="question_' + question.id + '" value="No" ' + selectedNo + ' disabled> No</label></div>');
                        }

                        $('#answersForm').append(formGroup);
                    });
                    // Add hidden fields for todoId, taskId, and hrId
                    $('#questionsForm').append('<input type="hidden" name="category" value="' + QuestionCategory + '">');
                    $('#questionsForm').append('<input type="hidden" name="todoid" value="' + todoId + '">');
                    $('#questionsForm').append('<input type="hidden" name="hrId" value="' + hrId + '">');
                    $('#questionsForm').append('<input type="hidden" name="taskid" value="' + response.tasks.id + '">');
                    $('#questionsForm').append('<input type="hidden" name="submited_by" value="' + <?php echo $staffid; ?> + '">');
                    $('#questionsForm').append('<input type="hidden" name="' + csrfName + '" value="' + csrfHash + '">');
                    $('#questionsForm').append('<button type="submit" class="btn btn-primary" id="' + (buttonType === 'confirmbtn' ? 'confirmbtn' : 'Submitbtn') + '">' + (buttonType === 'confirmbtn' ? 'Yes, I confirm.' : 'Submit') + '</button>');

                    // Show the modal if questions are available
                    if (response.questions.length > 0) {
                        $('#questionsModal').modal('show');
                    }
                }
            },
            error: function() {
                alert_float("danger", 'An error occurred while fetching the questions.');
            }
        });
    }
</script>
</html>