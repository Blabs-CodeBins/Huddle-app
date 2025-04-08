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
        margin-bottom: 10px;
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

    /* .scrollable-container {
        max-height: 264px;
        min-height: 264px;
        overflow-y: auto;
    } */
    .scrollable-container {
        max-height: 450px;
        /*264px; */
        /* min-height: 450px; */
        min-height: 175px;
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

    .col-md-1.checkbox-sec {
        padding: 0px 0px 0px 15px;
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

    .todocheckbox {
        cursor: pointer;
    }

    #next_button {
        margin-top: 10px;
        /* Adjust as needed */
        font-size: 24px;
        /* Adjust size of the arrow */
        width: 50px;
        /* Adjust width */
        height: 50px;
        /* Adjust height */
        line-height: 50px;
        /* Vertically center the arrow */
        border-radius: 50%;
        /* Make the button circular */
        border: 1px solid #ddd;
        /* Add a border */
        background-color: #f8f9fa;
        /* Light background color */
        display: flex;
        justify-content: center;
        align-items: center;
        position: fixed;
        top: calc(50vh - 25px);
        right: calc(50% - 130px);
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

    .rightarrow i.fa.fa-long-arrow-right {
        color: green;
        font-size: 20px;
        cursor: pointer;
    }

    .leftarrow i.fa.fa-long-arrow-left {
        color: red;
        font-size: 20px;
        cursor: pointer;
    }

    #starttodaydaysContainer .panel.panel-default,
    .panel.panel-default.TodaystodoContainer {
        /* background-color: rgb(254 252 232); */
        background-color: rgb(240, 247, 255);
        /* border-color: hsl(53deg 98% 77% / 60%); */
        border-color: rgb(186, 200, 217);
    }

    #huddlerSOD .modal-body {
        height: 700px;
        overflow: auto;
        min-height: 700px;
    }

    .custom-table {
        width: 100%;
        max-width: 900px;
        /* margin: 0px;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden; */
    }

    .custom-table td {
        padding: 10px 30px 10px 0px;
        text-align: left;
        vertical-align: middle;
    }

    .custom-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .custom-table td span {
        display: block;
        font-size: 16px;
    }

    .icon {
        color: #007bff;
        margin-right: 10px;
    }

    .custom-table .icon.red {
        color: #dc3545;
    }

    .custom-table .icon.green {
        color: #28a745;
    }

    .custom-table td:not(:last-child) {
        border-right: 1px solid #eee;
    }

    .custom-table .icon.yellow {
        color: #ffc107;
    }

    .custom-table td:nth-child(1) .icon {
        color: #17a2b8;
    }

    #huddlerSOD .modal-header,
    #huddlerSOD .modal-content {
        background-color: #f1f5f9;
    }

    table.custom-table.panel_s {
        margin-bottom: 0px;
    }
</style>
<div id="wrapper">
    <div class="content" id="mytodos-content">
        <!-- Modal box -->
        <div class="modal fade" id="huddlerSOD" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xxl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="row">
                            <div class="col-md-12 tw-flex tw-justify-between">
                                <div class="heading-title col-md-5">
                                    <h3 class="tw-font-semibold tw-text-gray-800 tw-m-0">
                                        Today's Plan (<?php $TodayD = date('Y-m-d');
                                                        echo date('M jS, Y', strtotime($TodayD)); ?>)
                                        of: <?php echo (isset($staffull_name)) ? $staffull_name : '[my name]'; ?>
                                    </h3>
                                </div>
                                <div class="col-md-7 point-table">
                                    <table class="custom-table panel_s">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <span class="tw-text-gray-600 text-right"><i class="fas fa-calendar-day icon"></i>Points Assigned Today</span>
                                                    <h3 class="tw-font-semibold tw-text-gray-800 tw-m-0 text-right"><?php echo $QuantitativeReport['Points_Assigned_Today']; ?></h3>
                                                </td>
                                                <td>
                                                    <span class="tw-text-gray-600 text-right"><i class="fas fa-calendar-alt icon green"></i>Earned in <?php echo date('F'); ?></span>
                                                    <h3 class="tw-font-semibold tw-text-gray-800 tw-m-0 text-right"><?php echo $QuantitativeReport['Earned_This_Month']; ?></h3>
                                                </td>
                                                <td>
                                                    <span class="tw-text-gray-600 text-right"><i class="fas fa-calendar-check icon yellow"></i>Earned this Year</span>
                                                    <h3 class="tw-font-semibold tw-text-gray-800 tw-m-0 text-right"><?php echo $QuantitativeReport['Earned_This_Year']; ?></h3>
                                                </td>
                                                <td>
                                                    <span class="tw-text-gray-600 text-right"><i class="fas fa-exclamation-triangle icon red"></i>Points Due</span>
                                                    <h3 class="tw-font-semibold tw-text-gray-800 tw-m-0 text-right"><?php echo $QuantitativeReport['Points_Due']; ?></h3>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <?php
                                $todaysTodoSubmitted = $this->session->userdata('todays_todo_submitted');
                                $todaysTasks = $this->session->userdata('todaysTasks');
                                
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 left-card">
                                <?php $todayTaskIds = array_column($todaysToDo, 'task_id'); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel_s">
                                            <div class="panel-body scrollable-container" id="starttodaydaysContainer">
                                                <div class="page-header tw-mt-0 tw-mb-0 tw-pb-1 sticky-heading">
                                                    <!-- <h4>Need to Start Today</h4> -->
                                                    <h4>Task auto set for today</h4>
                                                </div>
                                                <div class="panel-group tw-mt-3">
                                                    <?php foreach ($dueToday as $task): ?>
                                                        <div class="panel panel-default" id="taskpanelRight<?php echo $task['id']; ?>">
                                                            <div class="row tw-flex tw-justify-between">
                                                                <div class="col-md-11 content-sec">
                                                                    <div class="row">
                                                                        <div class="col-md-12 task-name">
                                                                            <div class="task-name-status">
                                                                                <a href="<?php echo admin_url(); ?>/tasks/view/<?php echo $task['id']; ?>" onclick="init_task_modal(<?php echo $task['id']; ?>);return false;">
                                                                                    <span class="inline-block full-width tw-truncate">#<?php echo $task['id']; ?> - <?php echo $task['name']; ?></span>
                                                                                </a>
                                                                            </div>
                                                                            <?php if (isset($task['rel_id']) && $task['rel_id'] != NULL) { ?>
                                                                                <a class="tw-text-neutral-600 tw-truncate -tw-mt-1 tw-block tw-text-sm" data-toggle="tooltip" title="" href="<?php echo admin_url(); ?>/projects/view/<?php echo $task['rel_id']; ?>" data-original-title="Related To">#<?php echo $task['rel_id']; ?> - <?php echo $task['project_name']; ?></a>
                                                                            <?php } ?>
                                                                        </div>
                                                                        <div class="col-md-4 tw-text-neutral-600 mtop10 tw-text-sm">
                                                                            <div class="tw-flex -tw-space-x-1">
                                                                                <a href="<?php echo admin_url(); ?>profile/<?= $staffid ?>">
                                                                                    <?php 
                                                                                    if(isset($task['img_tag'])){
                                                                                        echo $task['img_tag'];
                                                                                    }else{
                                                                                        echo staff_profile_image($staffid, ['img', 'img-responsive', 'staff-profile-image-small', 'tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white'], 'small', ['data-toggle' => 'tooltip', 'data-title'  => $staffull_name,]);
                                                                                    }
                                                                                    ?>
                                                                                    <!-- <img data-toggle="tooltip" data-title="<?= $staffull_name ?>" src="http://localhost/PmpHuddler/pmpdev2/uploads/staff_profile_images/<?= $staffid ?>/small_<?= $staffull_profile ?>" class="tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white"> -->
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-8 text-right tw-text-neutral-500 mtop10 tw-text-sm">
                                                                            <?php
                                                                            // Determine the icon and color based on task status
                                                                            switch ($task['status']) {
                                                                                case 1:
                                                                                    $icon = 'fas fa-circle';
                                                                                    $color = 'text-muted';
                                                                                    $status_label = 'Not Started';
                                                                                    break;
                                                                                case 2:
                                                                                    $icon = 'fas fa-hourglass-half';
                                                                                    $color = 'text-warning';
                                                                                    $status_label = 'Awaiting Feedback';
                                                                                    break;
                                                                                case 3:
                                                                                    $icon = 'fas fa-vial';
                                                                                    $color = 'text-info';
                                                                                    $status_label = 'Testing';
                                                                                    break;
                                                                                case 4:
                                                                                    $icon = 'fas fa-sync-alt';
                                                                                    $color = 'text-primary';
                                                                                    $status_label = 'In Progress';
                                                                                    break;
                                                                                case 5:
                                                                                    $icon = 'fas fa-check-circle';
                                                                                    $color = 'text-success';
                                                                                    $status_label = 'Completed';
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
                                                                            <span class="<?php echo ($task['startdate'] < $TodayD) ? 'text-danger' : 'tw-text-neutral-500';?>" data-toggle="tooltip" title="" data-original-title="Start Date">
                                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                                <?php echo date('d-m-Y', strtotime($task['startdate'])); ?>
                                                                            </span>
                                                                            &nbsp;
                                                                            <span class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Due Date">
                                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                                <?php echo date('d-m-Y', strtotime($task['duedate'])); ?>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1 rightarrow !tw-p-0 hide">
                                                                    <span data-taskid="<?php echo $task['id']; ?>" class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Move Right">
                                                                        <i class="fa fa-long-arrow-right" aria-hidden="true" id="rightarrow<?php echo $task['id']; ?>"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <?php if(count($dueToday) == 0): ?>
                                                        <div class="col-md-12">
                                                            <div class="alert alert-warning d-flex align-items-center tw-p-1 tw-mb-0" role="alert">
                                                                <i class="glyphicon glyphicon-info-sign" style="margin-right: 8px;"></i>
                                                                <span>No tasks auto-assigned for today. Please check with your PM/TL if you need any assignments.</span>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="panel_s">
                                            <div class="panel-body scrollable-container" id="next5daysContainer">
                                                <div class="page-header tw-mt-0 tw-mb-0 tw-pb-1 sticky-heading">
                                                    <h4>Starts in Next 5 days</h4>
                                                </div>
                                                <div class="panel-group tw-mt-3">
                                                    <?php foreach ($dueNext5Days as $task): ?>
                                                        <div class="panel panel-default" id="taskpanelRight<?php echo $task['id']; ?>">
                                                            <div class="row tw-flex tw-justify-between">
                                                                <div class="col-md-11 content-sec">
                                                                    <div class="row">
                                                                        <div class="col-md-12 task-name">
                                                                            <div class="task-name-status">
                                                                                <a href="<?php echo admin_url(); ?>/tasks/view/<?php echo $task['id']; ?>" onclick="init_task_modal(<?php echo $task['id']; ?>);return false;">
                                                                                    <span class="inline-block full-width tw-truncate">#<?php echo $task['id']; ?> - <?php echo $task['name']; ?></span>
                                                                                </a>
                                                                            </div>
                                                                            <a class="tw-text-neutral-600 tw-truncate -tw-mt-1 tw-block tw-text-sm" data-toggle="tooltip" title="" href="<?php echo admin_url(); ?>/projects/view/<?php echo $task['rel_id']; ?>" data-original-title="Related To">#<?php echo $task['rel_id']; ?> - <?php echo $task['project_name']; ?></a>
                                                                        </div>
                                                                        <div class="col-md-4 tw-text-neutral-600 mtop10 tw-text-sm">
                                                                            <div class="tw-flex -tw-space-x-1">
                                                                                <a href="<?php echo admin_url(); ?>profile/<?= $staffid ?>">
                                                                                    <?php 
                                                                                        if(isset($task['img_tag'])){
                                                                                            echo $task['img_tag'];
                                                                                        }else{
                                                                                            echo staff_profile_image($staffid, ['img', 'img-responsive', 'staff-profile-image-small', 'tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white'], 'small', ['data-toggle' => 'tooltip', 'data-title'  => $staffull_name,]);
                                                                                        }
                                                                                    ?>
                                                                                    <!-- <img data-toggle="tooltip" data-title="<?= $staffull_name ?>" src="http://localhost/PmpHuddler/pmpdev2/uploads/staff_profile_images/<?= $staffid ?>/small_<?= $staffull_profile ?>" class="tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white"> -->
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-8 text-right tw-text-neutral-500 mtop10 tw-text-sm">
                                                                            <?php
                                                                            // Determine the icon and color based on task status
                                                                            switch ($task['status']) {
                                                                                case 1:
                                                                                    $icon = 'fas fa-circle';
                                                                                    $color = 'text-muted';
                                                                                    $status_label = 'Not Started';
                                                                                    break;
                                                                                case 2:
                                                                                    $icon = 'fas fa-hourglass-half';
                                                                                    $color = 'text-warning';
                                                                                    $status_label = 'Awaiting Feedback';
                                                                                    break;
                                                                                case 3:
                                                                                    $icon = 'fas fa-vial';
                                                                                    $color = 'text-info';
                                                                                    $status_label = 'Testing';
                                                                                    break;
                                                                                case 4:
                                                                                    $icon = 'fas fa-sync-alt';
                                                                                    $color = 'text-primary';
                                                                                    $status_label = 'In Progress';
                                                                                    break;
                                                                                case 5:
                                                                                    $icon = 'fas fa-check-circle';
                                                                                    $color = 'text-success';
                                                                                    $status_label = 'Completed';
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
                                                                                <?php echo date('d-m-Y', strtotime($task['startdate'])); ?>
                                                                            </span>
                                                                            &nbsp;
                                                                            <span class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Due Date">
                                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                                <?php echo date('d-m-Y', strtotime($task['duedate'])); ?>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1 rightarrow !tw-p-0">
                                                                    <span data-taskid="<?php echo $task['id']; ?>" class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Move Right">
                                                                        <i class="fa fa-long-arrow-right" aria-hidden="true" id="rightarrow<?php echo $task['id']; ?>"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <?php if(count($dueNext5Days) == 0): ?>
                                                        <div class="col-md-12">
                                                            <div class="alert alert-warning d-flex align-items-center tw-p-1 tw-mb-0" role="alert">
                                                                <i class="glyphicon glyphicon-info-sign" style="margin-right: 8px;"></i>
                                                                <span>No upcoming tasks within the next 5 days. Stay prepared for any new assignments.</span>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="panel_s">
                                            <div class="panel-body scrollable-container" id="next25daysContainer" <?php (empty($dueNext25Days)) ? 'style="min-height: 535px;"' : ' '; ?>>
                                                <div class="page-header tw-mt-0 tw-mb-0 tw-pb-1 sticky-heading">
                                                    <h4>Starts in Next 25 days</h4>
                                                </div>
                                                <div class="panel-group tw-mt-3">
                                                    <?php foreach ($dueNext25Days as $task): ?>
                                                        <div class="panel panel-default" id="taskpanelRight<?php echo $task['id']; ?>">
                                                            <div class="row tw-flex tw-justify-between">
                                                                <div class="col-md-11 content-sec">
                                                                    <div class="row">
                                                                        <div class="col-md-12 task-name">
                                                                            <div class="task-name-status">
                                                                                <a href="<?php echo admin_url(); ?>/tasks/view/<?php echo $task['id']; ?>" onclick="init_task_modal(<?php echo $task['id']; ?>);return false;">
                                                                                    <span class="inline-block full-width tw-truncate">#<?php echo $task['id']; ?> - <?php echo $task['name']; ?></span>
                                                                                </a>
                                                                            </div>
                                                                            <a class="tw-text-neutral-600 tw-truncate -tw-mt-1 tw-block tw-text-sm" data-toggle="tooltip" title="" href="<?php echo admin_url(); ?>/projects/view/<?php echo $task['rel_id']; ?>" data-original-title="Related To">#<?php echo $task['rel_id']; ?> - <?php echo $task['project_name']; ?></a>
                                                                        </div>
                                                                        <div class="col-md-4 tw-text-neutral-600 mtop10 tw-text-sm">
                                                                            <div class="tw-flex -tw-space-x-1">
                                                                                <a href="<?php echo admin_url(); ?>profile/<?= $staffid ?>">
                                                                                    <?php 
                                                                                        if(isset($task['img_tag'])){
                                                                                            echo $task['img_tag'];
                                                                                        }else{
                                                                                            echo staff_profile_image($staffid, ['img', 'img-responsive', 'staff-profile-image-small', 'tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white'], 'small', ['data-toggle' => 'tooltip', 'data-title'  => $staffull_name,]);
                                                                                        }
                                                                                    ?>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-8 text-right tw-text-neutral-500 mtop10 tw-text-sm">
                                                                            <?php
                                                                            // Determine the icon and color based on task status
                                                                            switch ($task['status']) {
                                                                                case 1:
                                                                                    $icon = 'fas fa-circle';
                                                                                    $color = 'text-muted';
                                                                                    $status_label = 'Not Started';
                                                                                    break;
                                                                                case 2:
                                                                                    $icon = 'fas fa-hourglass-half';
                                                                                    $color = 'text-warning';
                                                                                    $status_label = 'Awaiting Feedback';
                                                                                    break;
                                                                                case 3:
                                                                                    $icon = 'fas fa-vial';
                                                                                    $color = 'text-info';
                                                                                    $status_label = 'Testing';
                                                                                    break;
                                                                                case 4:
                                                                                    $icon = 'fas fa-sync-alt';
                                                                                    $color = 'text-primary';
                                                                                    $status_label = 'In Progress';
                                                                                    break;
                                                                                case 5:
                                                                                    $icon = 'fas fa-check-circle';
                                                                                    $color = 'text-success';
                                                                                    $status_label = 'Completed';
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
                                                                                <?php echo date('d-m-Y', strtotime($task['startdate'])); ?>
                                                                            </span>
                                                                            &nbsp;
                                                                            <span class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Due Date">
                                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                                <?php echo date('d-m-Y', strtotime($task['duedate'])); ?>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1 rightarrow !tw-p-0">
                                                                    <span data-taskid="<?php echo $task['id']; ?>" class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Move Right">
                                                                        <i class="fa fa-long-arrow-right" aria-hidden="true" id="rightarrow<?php echo $task['id']; ?>"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <?php if(count($dueNext25Days) == 0): ?>
                                                        <div class="col-md-12">
                                                            <div class="alert alert-warning d-flex align-items-center tw-p-1 tw-mb-0" role="alert">
                                                                <i class="glyphicon glyphicon-info-sign" style="margin-right: 8px;"></i>
                                                                <span>No tasks scheduled for the next 25 days. Check with your PM/TL for upcoming plans.</span>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 right-card">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel_s">
                                            <div class="panel-body scrollable-container">
                                                <div class="page-header tw-mt-0 tw-mb-0 tw-pb-1 sticky-heading">
                                                    <h4>Already Overdue</h4>
                                                </div>
                                                <div class="panel-group tw-mt-3">
                                                    <?php foreach ($overdueTask as $task): ?>
                                                        <div class="panel panel-default overdue-panel">
                                                            <div class="row tw-flex tw-justify-between">
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
                                                                                <a href="<?php echo admin_url(); ?>profile/<?= $staffid ?>">
                                                                                    <?php 
                                                                                        if(isset($task['img_tag'])){
                                                                                            echo $task['img_tag'];
                                                                                        }else{
                                                                                            echo staff_profile_image($staffid, ['img', 'img-responsive', 'staff-profile-image-small', 'tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white'], 'small', ['data-toggle' => 'tooltip', 'data-title'  => $staffull_name,]);
                                                                                        }
                                                                                    ?>
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
                                    <div class="col-md-12">
                                        <div class="panel_s">
                                            <div class="panel-body scrollable-container" id="TodaystodoContainer">
                                                <div class="page-header tw-mt-0 tw-mb-0 tw-pb-1 sticky-heading">
                                                    <h4>Today's To-Do</h4>
                                                </div>
                                                <div class="panel-group tw-mt-3" id="huddlerTodos">
                                                    <?php foreach ($todaysToDo as $task): ?>
                                                        <div class="panel panel-default" id="taskpanelLeft<?php echo $task['task_id']; ?>">
                                                            <div class="row tw-flex tw-justify-between">
                                                                <?php if ($task['start_date'] > (date('Y-m-d')) && $task['status'] == 2) { ?>
                                                                    <div class="col-md-1 leftarrow">
                                                                        <span data-taskid="<?php echo $task['task_id']; ?>" class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Remove">
                                                                            <i class="fa fa-long-arrow-left" aria-hidden="true" id="leftarrow<?php echo $task['task_id']; ?>"></i>
                                                                        </span>
                                                                    </div>
                                                                <?php } ?>
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
                                                                                <a href="<?php echo admin_url(); ?>profile/<?= $staffid ?>">
                                                                                    <?php 
                                                                                        if(isset($task['img_tag'])){
                                                                                            echo $task['img_tag'];
                                                                                        }else{
                                                                                            echo staff_profile_image($staffid, ['img', 'img-responsive', 'staff-profile-image-small', 'tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white'], 'small', ['data-toggle' => 'tooltip', 'data-title'  => $staffull_name,]);
                                                                                        }
                                                                                    ?>
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
                                                                            <span class="<?php echo ($task['start_date'] < $TodayD) ? 'text-danger' : 'tw-text-neutral-500';?>" data-toggle="tooltip" title="" data-original-title="Start Date">
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
                                                    <?php foreach ($dueToday as $task): ?>
                                                        <div class="panel panel-default TodaystodoContainer" id="taskpanelRight<?php echo $task['id']; ?>">
                                                            <div class="row tw-flex tw-justify-between">
                                                                <div class="col-md-11 content-sec">
                                                                    <div class="row">
                                                                        <div class="col-md-12 task-name">
                                                                            <div class="task-name-status">
                                                                                <a href="<?php echo admin_url(); ?>/tasks/view/<?php echo $task['id']; ?>" onclick="init_task_modal(<?php echo $task['id']; ?>);return false;">
                                                                                    <span class="inline-block full-width tw-truncate">#<?php echo $task['id']; ?> - <?php echo $task['name']; ?></span>
                                                                                </a>
                                                                            </div>
                                                                            <?php if (isset($task['rel_id']) && $task['rel_id'] != NULL) { ?>
                                                                                <a class="tw-text-neutral-600 tw-truncate -tw-mt-1 tw-block tw-text-sm" data-toggle="tooltip" title="" href="<?php echo admin_url(); ?>/projects/view/<?php echo $task['rel_id']; ?>" data-original-title="Related To">#<?php echo $task['rel_id']; ?> - <?php echo $task['project_name']; ?></a>
                                                                            <?php } ?>
                                                                        </div>
                                                                        <div class="col-md-4 tw-text-neutral-600 mtop10 tw-text-sm">
                                                                            <div class="tw-flex -tw-space-x-1">
                                                                                <a href="<?php echo admin_url(); ?>profile/<?= $staffid ?>">
                                                                                    <?php 
                                                                                        if(isset($task['img_tag'])){
                                                                                            echo $task['img_tag'];
                                                                                        }else{
                                                                                            echo staff_profile_image($staffid, ['img', 'img-responsive', 'staff-profile-image-small', 'tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white'], 'small', ['data-toggle' => 'tooltip', 'data-title'  => $staffull_name,]);
                                                                                        }
                                                                                    ?>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-8 text-right tw-text-neutral-500 mtop10 tw-text-sm">

                                                                            <span class="text-info" data-toggle="tooltip" title="" data-original-title="Status">
                                                                                <i class="fas fa-user-check"></i> Assigned</span>
                                                                            </span>
                                                                            &nbsp;
                                                                            <span class="<?php echo ($task['startdate'] < $TodayD) ? 'text-danger' : 'tw-text-neutral-500';?>" data-toggle="tooltip" title="" data-original-title="Start Date">
                                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                                <?php echo date('d-m-Y', strtotime($task['startdate'])); ?>
                                                                            </span>
                                                                            &nbsp;
                                                                            <span class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Due Date">
                                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                                <?php echo date('d-m-Y', strtotime($task['duedate'])); ?>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <div id="virtual-todo">
                                                    </div>
                                                    <?php if(count($todaysToDo) == 0 && count($dueToday) == 0): ?>
                                                        <div class="col-md-12">
                                                            <div class="alert alert-warning d-flex align-items-center tw-p-1 tw-mb-0" role="alert">
                                                                <i class="glyphicon glyphicon-info-sign" style="margin-right: 8px;"></i>
                                                                <span>No tasks listed in your To-Do for today. Request tasks from your PM/TL if needed.</span>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <?php
                                                $automaticaddTaskid = array_column($dueToday, 'id');

                                                if (!empty($todayTaskIds) && is_array($todayTaskIds)) {
                                                    $allTaskIds = array_merge($automaticaddTaskid, $todayTaskIds);
                                                } else {
                                                    $allTaskIds = $automaticaddTaskid;
                                                }

                                                $taskIdsString = implode(',', $allTaskIds);
                                                ?>
                                                <input type="hidden" name="todaystodotaskid" value="<?php echo $taskIdsString; ?>">
                                                <input type="hidden" name="removetodotaskid" value="">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="panel_s">
                                            <div class="panel-body scrollable-container">
                                                <div class="page-header tw-mt-0 tw-mb-0 tw-pb-1 sticky-heading">
                                                    <h4>Needs Attention</h4>
                                                </div>
                                                <div class="panel-group tw-mt-3">
                                                    <?php foreach ($needsAttention as $task): ?>
                                                        <div class="panel panel-default">
                                                            <div class="row tw-flex ">
                                                                <div class="col-md-1 checkbox-sec hidden">
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
                                                                        <div class="task-action col-lg-1 col-sm-4 !tw-p-0 hidden">
                                                                            <div class="dropdown">
                                                                                <button class="btn btn-sm dropdown-toggle actionMenu" type="button" id="actionMenu<?php echo $task['task_id']; ?>" data-toggle="dropdown" aria-expanded="true" style="color:#3b82f6;border:1px solid #b1cdfb;background: #f7faff;">
                                                                                    Action <span class="caret"></span>
                                                                                </button>
                                                                                <?php
                                                                                $statuses = [
                                                                                    'Mark as Complete' => 1,
                                                                                    'Bug Found' => 2,
                                                                                    'New Work' => 3,
                                                                                    'No Problem' => 4
                                                                                ];
                                                                                $currentStatus = $task['needs_attention_status'];
                                                                                echo '<ul class="dropdown-menu dropdown-menu-left" aria-labelledby="actionMenu' . $task['task_id'] . '">';
                                                                                foreach ($statuses as $status => $actionCode) {
                                                                                    if ($currentStatus != $status) {
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
                                                                                    <?php 
                                                                                        if(isset($task['img_tag'])){
                                                                                            echo $task['img_tag'];
                                                                                        }else{
                                                                                            echo staff_profile_image($staffid, ['img', 'img-responsive', 'staff-profile-image-small', 'tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white'], 'small', ['data-toggle' => 'tooltip', 'data-title'  => $staffull_name,]);
                                                                                        }
                                                                                    ?>
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
                                                    <?php if(count($needsAttention) == 0 ): ?>
                                                        <div class="col-md-12">
                                                            <div class="alert alert-warning d-flex align-items-center tw-mt-3 tw-p-1 tw-mb-0" role="alert">
                                                                <i class="glyphicon glyphicon-info-sign" style="margin-right: 8px;"></i>
                                                                <span>No tasks need immediate attention.</span>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (($isProjectManager || $istoplinemanager) && (!empty($helpRequests) || !empty($toApprove))) { ?>
                                        <div class="col-md-12">
                                            <div class="panel_s">
                                                <div class="panel-body scrollable-container">
                                                    <div class="page-header tw-mt-0 tw-mb-0 tw-pb-1 sticky-heading">
                                                        <h4>Help Requested</h4>
                                                    </div>
                                                    <div class="panel-group tw-mt-3">
                                                        <?php foreach ($helpRequests as $task): ?>
                                                            <div class="panel panel-default hide">
                                                                <div class="tw-flex tw-justify-between">
                                                                    <div class="checkbox-sec">
                                                                        <input type="checkbox" id="helpRequest1" name="helpRequest" value="1" class="todocheckbox">
                                                                    </div>
                                                                    <div class="middle-sec">
                                                                        <div class="task_name">
                                                                            <span>#64 </span>- <span class="helptaskname">Review Project Plan</span>
                                                                        </div>
                                                                        <div class="task_remarks">
                                                                            No remarks available </div>
                                                                    </div>
                                                                    <div class="action-sec">
                                                                        <div class="task_status">
                                                                            <i class="fas fa-check-circle text-success"></i>
                                                                            <span class="text-success">Completed</span>
                                                                        </div>

                                                                        <div class="task_view">
                                                                            <a href="#" class="tw-text-muted view-help-request" data-id="1" data-toggle="modal" data-target="#helprequestsModal1">View</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="panel panel-default">
                                                                <div class="row tw-flex tw-justify-between">
                                                                    <div class="col-md-1 checkbox-sec hide">
                                                                        <input type="checkbox" id="todotask<?php echo $task['task_id']; ?>" name="todotask[]" value="<?php echo $task['task_id']; ?>" class="todocheckbox"
                                                                            <?php echo in_array($task['task_id'], $todayTaskIds) ? 'checked' : ''; ?>>
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
                                                                                    <a href="<?php echo admin_url(); ?>profile/<?= $staffid ?>">
                                                                                        <?php 
                                                                                            if(isset($task['img_tag'])){
                                                                                                echo $task['img_tag'];
                                                                                            }else{
                                                                                                echo staff_profile_image($staffid, ['img', 'img-responsive', 'staff-profile-image-small', 'tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white'], 'small', ['data-toggle' => 'tooltip', 'data-title'  => $staffull_name,]);
                                                                                            }
                                                                                        ?>
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
                                                                                <span class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Help Requested At">
                                                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                                    <?php echo date('d-m-Y', strtotime($task['helpreq_at'])); ?>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        <?php if(count($helpRequests) == 0 ): ?>
                                                            <div class="col-md-12">
                                                                <div class="alert alert-warning d-flex align-items-center tw-mt-3 tw-p-1 tw-mb-0" role="alert">
                                                                    <i class="glyphicon glyphicon-info-sign" style="margin-right: 8px;"></i>
                                                                    <span>No help requests from employees at the moment. Keep an eye on this section in case someone needs assistance.</span>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="panel_s">
                                                <div class="panel-body scrollable-container" id="ToApproveContainer">
                                                    <div class="page-header tw-mt-0 tw-mb-0 tw-pb-1 sticky-heading">
                                                        <h4>To Approve</h4>
                                                    </div>
                                                    <div class="panel-group tw-mt-3" id="huddlerTodos">
                                                        <?php foreach ($toApprove as $task): ?>
                                                            <div class="panel panel-default hide">
                                                                <div class="row tw-flex tw-justify-between">
                                                                    <div class="col-md-1 checkbox-sec hide">
                                                                        <input type="checkbox" id="todotask<?php echo $task['id']; ?>" name="todotask[]" value="<?php echo $task['id']; ?>" class="todocheckbox">
                                                                    </div>
                                                                    <div class="col-md-4 middle-sec">
                                                                        <div class="task_name">
                                                                            <span>#<?php echo $task['task_id']; ?> </span>- <span class="helptaskname"><?php echo $task['task_name']; ?></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3 task_duedate tw-pr-2">
                                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                        <span class="text-muted"><?php echo date('d-m-Y', strtotime($task['due_date'])); ?></span>
                                                                    </div>
                                                                    <div class="col-md-2 task_status">
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
                                                                                $icon = 'fas fa-check-circle';
                                                                                $color = 'text-success';
                                                                                $status_label = 'Complete';
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
                                                                                $status_label = 'Needs Attention';
                                                                                break;
                                                                            default:
                                                                                $icon = 'fas fa-question-circle';
                                                                                $color = 'text-muted';
                                                                                $status_label = 'Unknown';
                                                                        }

                                                                        ?>
                                                                        <i class="<?php echo $icon; ?> <?php echo $color; ?>"></i>
                                                                        <!-- <span class="<?php echo $color; ?>"><?php echo $status_label; ?></span> -->
                                                                    </div>
                                                                    <div class="col-md-2 action-sec">
                                                                        <div class="move-action tw-flex tw-justify-between">
                                                                            <label>
                                                                                <input type="radio" name="action_<?php echo $task['id']; ?>" value="approve" />
                                                                                <i class="fas fa-check text-success"></i>
                                                                            </label>
                                                                            <label>
                                                                                <input type="radio" name="action_<?php echo $task['id']; ?>" value="reject" />
                                                                                <i class="fas fa-times text-danger"></i>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="panel panel-default">
                                                                <div class="row tw-flex tw-justify-between">
                                                                    <div class="col-md-1 checkbox-sec hide">
                                                                        <input type="checkbox" id="todotask<?php echo $task['task_id']; ?>" name="todotask[]" value="<?php echo $task['task_id']; ?>" class="todocheckbox"
                                                                            <?php echo in_array($task['task_id'], $todayTaskIds) ? 'checked' : ''; ?>>
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
                                                                                    <a href="<?php echo admin_url(); ?>profile/<?= $staffid ?>">
                                                                                        <?php 
                                                                                            if(isset($task['img_tag'])){
                                                                                                echo $task['img_tag'];
                                                                                            }else{
                                                                                                echo staff_profile_image($staffid, ['img', 'img-responsive', 'staff-profile-image-small', 'tw-h-5 tw-w-5 tw-inline-block tw-rounded-full tw-ring-2 tw-ring-white'], 'small', ['data-toggle' => 'tooltip', 'data-title'  => $staffull_name,]);
                                                                                            }
                                                                                        ?>
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
                                                                                        $reportsToArray = array_column($reportingUsers, 'reports_to');
                                                                                        $status_label = (in_array($staffid, $reportsToArray)) ? 'My Approval Pending' : $staffull_name . ' Approval Pending';
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
                                                        <?php if(count($toApprove) == 0 ): ?>
                                                            <div class="col-md-12">
                                                                <div class="alert alert-warning d-flex align-items-center tw-mt-3 tw-p-1 tw-mb-0" role="alert">
                                                                    <i class="glyphicon glyphicon-info-sign" style="margin-right: 8px;"></i>
                                                                    <span>No pending tasks for approval. All completed tasks have been reviewed successfully.</span>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                    
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-lg <?php echo $countdata == 0 ? ' hidden' : ''; ?>" id="submitmytodayplan">Submit My Today's Plan</button>
                                <?php echo $countdata == 0 ? ' <p class="text-danger bold"> Request today’s tasks from your PM/TL. </p>' : ''; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<?php init_tail(); ?>
<script>
    $(document).ready(function() {
        $('#huddlerSOD').modal('show');
        // Disable all keyboard inputs except for inside the modal
        $(document).on('keydown', function(event) {
            var target = $(event.target);
            if (!target.closest('#huddlerSOD').length) {
                event.preventDefault();
            }
        });


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
        $(document).on('click', '.rightarrow', function() {
            var taskId = $(this).find('.tw-text-neutral-500').data('taskid');
            console.log(taskId);
            var taskIds = $('input[name="todaystodotaskid"]').val();
            var taskpanelRight = '#taskpanelRight' + taskId;
            var taskIdArray = taskIds ? taskIds.split(',').filter(Boolean) : [];
            if (!taskIdArray.includes(String(taskId))) {
                // Add the taskId to the array
                taskIdArray.push(taskId);
            }
            $('input[name="todaystodotaskid"]').val(taskIdArray.join(','));

            $.ajax({
                url: '<?php echo admin_url(); ?>todo/GetTaskbyIds',
                type: 'POST',
                data: {
                    task_ids: taskId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Append new tasks
                        response.tasks.forEach(task => {
                            var icon, color, statusLabel;
                            // Determine the icon and color based on task status
                            switch (task.status) {
                                case '1':
                                    icon = 'fas fa-user-times';
                                    color = 'text-muted';
                                    statusLabel = 'Not Assigned';
                                    break;
                                case '2':
                                    icon = 'fas fa-user-check';
                                    color = 'text-info';
                                    statusLabel = 'Assigned';
                                    break;
                                case '3':
                                    icon = 'fas fa-check-circle';
                                    color = 'text-success';
                                    statusLabel = 'Complete';
                                    break;
                                case '4':
                                    icon = 'fas fa-thumbs-up';
                                    color = 'text-primary';
                                    statusLabel = 'PM Approved';
                                    break;
                                case '5':
                                    icon = 'fas fa-user-edit';
                                    color = 'text-danger';
                                    statusLabel = 'Re Assigned';
                                    break;
                                case '6':
                                    icon = 'fas fa-hands-helping';
                                    color = 'text-warning';
                                    statusLabel = 'Help Request';
                                    break;
                                case '7':
                                    icon = 'fas fa-exclamation-triangle';
                                    color = 'text-danger';
                                    statusLabel = 'Needs Attention';
                                    break;
                                default:
                                    icon = 'fas fa-question-circle';
                                    color = 'text-muted';
                                    statusLabel = 'Unknown';
                            }
                            // Create HTML for each task
                            if (task.rel_id != "" && task.rel_id != null) {
                                var projectHtml = `<a class="tw-text-neutral-600 tw-truncate -tw-mt-1 tw-block tw-text-sm" data-toggle="tooltip" title="" href="<?php echo admin_url(); ?>/projects/view/${task.rel_id}" data-original-title="Related To">#${task.rel_id} - ${task.project_name}</a>`;
                            } else {
                                var projectHtml = '';
                            }
                            var taskHtmlforleft = `
                                <div class="panel panel-default" id="taskpanelLeft${task.id}">
                                    <div class="row tw-flex tw-justify-between">
                                        <div class="col-md-1 leftarrow">
                                            <span data-taskid="${task.id}" class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Remove">
                                                <i class="fa fa-long-arrow-left" aria-hidden="true" id="leftarrow${task.id}"></i>
                                            </span>
                                        </div>
                                        <div class="col-md-11 content-sec">
                                            <div class="row">
                                                <div class="col-md-12 task-name">
                                                    <div class="task-name-status">
                                                        <a href="<?php echo admin_url(); ?>/tasks/view/${task.id}" onclick="init_task_modal(${task.id});return false;">
                                                            <span class="inline-block full-width tw-truncate">#${task.id} - ${task.name}</span>
                                                        </a>
                                                    </div>
                                                    ${projectHtml}
                                                </div>
                                                <div class="col-md-4 tw-text-neutral-600 mtop10 tw-text-sm">
                                                    <div class="tw-flex -tw-space-x-1">
                                                        <a href="<?php echo admin_url(); ?>profile/<?= $staffid ?>">
                                                            ${task.img_tag}
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-md-8 text-right tw-text-neutral-500 mtop10 tw-text-sm">
                                                    <span class="text-info" data-toggle="tooltip" title="Status">
                                                        <i class="fas fa-user-check"></i> Assigned
                                                    </span>
                                                    &nbsp;
                                                    <span class="tw-text-neutral-500" data-toggle="tooltip" title="Start Date">
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    ${new Date(task.startdate).toLocaleDateString('en-GB', {
                                                    day: '2-digit',
                                                    month: '2-digit',
                                                    year: 'numeric'
                                                    }).replace(/\//g, '-')}

                                                </span>
                                                &nbsp;
                                                    <span class="tw-text-neutral-500" data-toggle="tooltip" title="Due Date">
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    ${new Date(task.duedate).toLocaleDateString('en-GB', {
                                                        day: '2-digit',
                                                        month: '2-digit',
                                                        year: 'numeric'
                                                        }).replace(/\//g, '-')}

                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                            $('#TodaystodoContainer #huddlerTodos').append(taskHtmlforleft);
                            $(taskpanelRight).empty().slideDown('fast');
                            $(taskpanelRight).hide();

                        });
                    } else {
                        // Handle the error case
                        alert_float("danger", response.message);
                    }
                },
                error: function(error) {
                    alert_float("danger", 'An error occurred while creating Today\'s Plan.');
                }
            });

        });
        $(document).on('click', '.leftarrow', function() {
            var taskId = $(this).find('.tw-text-neutral-500').data('taskid');
            var taskpanelLeft = '#taskpanelLeft' + taskId;
            var taskpanelRight = '#taskpanelRight' + taskId;
            console.log(taskpanelRight);
            var taskpanelRightcount = $(taskpanelRight).length;
            console.log(taskpanelRightcount);

            var taskIds = $('input[name="todaystodotaskid"]').val();
            var taskIdArray = taskIds ? taskIds.split(',').filter(Boolean) : [];
            var removedTaskIds = $('input[name="removetodotaskid"]').val();
            var removedTaskIdArray = removedTaskIds ? removedTaskIds.split(',').filter(Boolean) : [];
            var index = taskIdArray.indexOf(String(taskId));
            if (index > -1) {
                taskIdArray.splice(index, 1);
                if (!removedTaskIdArray.includes(String(taskId))) {
                    removedTaskIdArray.push(String(taskId));
                }
            }
            $('input[name="todaystodotaskid"]').val(taskIdArray.join(','));
            $('input[name="removetodotaskid"]').val(removedTaskIdArray.join(','));


            $.ajax({
                url: '<?php echo admin_url(); ?>todo/GetTaskbyIds',
                type: 'POST',
                data: {
                    task_ids: taskId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Append new tasks
                        response.tasks.forEach(task => {
                            var icon, color, statusLabel;
                            // Determine the icon and color based on task status
                            switch (task.status) {
                                case '1':
                                    icon = 'fas fa-circle';
                                    color = 'text-muted';
                                    statusLabel = 'Not Started';
                                    break;
                                case '2':
                                    icon = 'fas fa-hourglass-half';
                                    color = 'text-warning';
                                    statusLabel = 'Awaiting Feedback';
                                    break;
                                case '3':
                                    icon = 'fas fa-vial';
                                    color = 'text-info';
                                    statusLabel = 'Testing';
                                    break;
                                case '4':
                                    icon = 'fas fa-sync-alt';
                                    color = 'text-primary';
                                    statusLabel = 'In Progress';
                                    break;
                                case '5':
                                    icon = 'fas fa-check-circle';
                                    color = 'text-success';
                                    statusLabel = 'Completed';
                                    break;
                                default:
                                    icon = 'fas fa-question-circle';
                                    color = 'text-muted';
                                    statusLabel = 'Unknown';
                            }

                            // Create HTML for each task
                            if (task.rel_id != "" && task.rel_id != null) {
                                var projectHtml = `<a class="tw-text-neutral-600 tw-truncate -tw-mt-1 tw-block tw-text-sm" data-toggle="tooltip" title="" href="<?php echo admin_url(); ?>/projects/view/${task.rel_id}" data-original-title="Related To">#${task.rel_id} - ${task.project_name}</a>`;
                            } else {
                                var projectHtml = '';
                            }
                            var taskHtmlforright = `
                            
                                <div class="row tw-flex tw-justify-between">
                                    <div class="col-md-11 content-sec">
                                        <div class="row">
                                            <div class="col-md-12 task-name">
                                                <div class="task-name-status">
                                                    <a href="<?php echo admin_url(); ?>/tasks/view/${task.id}" onclick="init_task_modal(${task.id});return false;">
                                                        <span class="inline-block full-width tw-truncate">#${task.id} - ${task.name}</span>
                                                    </a>
                                                </div>
                                                ${projectHtml}
                                            </div>
                                            <div class="col-md-4 tw-text-neutral-600 mtop10 tw-text-sm">
                                                <div class="tw-flex -tw-space-x-1">
                                                    <a href="<?php echo admin_url(); ?>profile/<?= $staffid ?>">
                                                        ${task.img_tag}
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-md-8 text-right tw-text-neutral-500 mtop10 tw-text-sm">
                                                <span class="text-primary" data-toggle="tooltip" title="Status">
                                                    <i class="fas fa-user-check"></i> In Progress
                                                </span>
                                                &nbsp;
                                                <span class="tw-text-neutral-500" data-toggle="tooltip" title="Start Date">
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                ${new Date(task.startdate).toLocaleDateString('en-GB', {
                                                    day: '2-digit',
                                                    month: '2-digit',
                                                    year: 'numeric'
                                                    }).replace(/\//g, '-')}

                                                </span>
                                                &nbsp;
                                                <span class="tw-text-neutral-500" data-toggle="tooltip" title="Due Date">
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                ${new Date(task.duedate).toLocaleDateString('en-GB', {
                                                    day: '2-digit',
                                                    month: '2-digit',
                                                    year: 'numeric'
                                                    }).replace(/\//g, '-')}

                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 rightarrow !tw-p-0">
                                        <span data-taskid="${task.id}" class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Move Right">
                                            <i class="fa fa-long-arrow-right" aria-hidden="true" id="rightarrow${task.id}"></i>
                                        </span>
                                    </div>
                                </div>
                            `;
                            // Get today's date
                            var today = new Date().toISOString().slice(0, 10);
                            var next5Days = new Date();
                            next5Days.setDate(new Date().getDate() + 5);
                            next5Days = next5Days.toISOString().slice(0, 10);

                            console.log(today);
                            console.log(next5Days);
                            $panelHtml = `<div class="panel panel-default" id="taskpanelRight${task.id}"> </div>`;
                            if (taskpanelRightcount === 0) {
                                if (task.startdate > today && task.startdate <= next5Days) {
                                    $('#next5daysContainer').find('.panel-group.tw-mt-3').append($panelHtml);
                                    //settimeout
                                    setTimeout(function() {
                                        $(taskpanelRight).show().slideDown('fast');
                                        $(taskpanelRight).append(taskHtmlforright);
                                        $(taskpanelLeft).remove().slideDown('fast');
                                    }, 100);
                                } else if (task.startdate > next5Days) {
                                    $('#next25daysContainer').find('.panel-group.tw-mt-3').append($panelHtml);
                                    setTimeout(function() {
                                        $(taskpanelRight).show().slideDown('fast');
                                        $(taskpanelRight).append(taskHtmlforright);
                                        $(taskpanelLeft).remove().slideDown('fast');
                                    }, 100);
                                }
                            } else {
                                $(taskpanelRight).show().slideDown('fast');
                                $(taskpanelRight).append(taskHtmlforright);
                                $(taskpanelLeft).remove().slideDown('fast');
                            }



                        });
                    } else {
                        // Handle the error case
                        alert_float("danger", response.message);
                    }
                },
                error: function(error) {
                    alert_float("danger", 'An error occurred while creating Today\'s Plan.');
                }
            });

        });
        $('input[name="lefttodotask[]"]').change(function() {
            var unselectedId = $(this).val();
            var removeSectionClass = ".virtual-" + unselectedId;
            console.log("removeSectionClass");
            var todaystodotaskid = $('input[name="todaystodotaskid"]').val();

            if (!$(this).is(':checked')) {
                // Remove the unselected task from the UI
                $('#huddlerTodos').find(removeSectionClass).remove();

                // Remove the unselected task from today's task IDs
                var selectedTasks = todaystodotaskid.split(',').filter(function(taskId) {
                    return taskId != unselectedId;
                });

                // Update the input value with the remaining task IDs
                $('input[name="todaystodotaskid"]').val(selectedTasks.join(','));
            }
        });
        $("#submitmytodayplan").click(function(e) {
            e.preventDefault();
            $('#mytodos-content').append('<div class="dt-loader"></div>');
            var todaystodotaskid = $('input[name="todaystodotaskid"]').val().split(',');
            var removetodotaskid = $('input[name="removetodotaskid"]').val().split(',');
            console.log(todaystodotaskid);
            var todaystodotaskid = $('input[name="todaystodotaskid"]').val().split(',').filter(Boolean);
            var removetodotaskid = $('input[name="removetodotaskid"]').val().split(',').filter(Boolean);
            console.log("Today's To-Do Task IDs: ", todaystodotaskid);
            console.log("Remove To-Do Task IDs: ", removetodotaskid);
            var filteredRemovetodotaskid = removetodotaskid.filter(function(id) {
                return !todaystodotaskid.includes(id);
            });
            console.log("Filtered Remove To-Do Task IDs: ", filteredRemovetodotaskid);

            $.ajax({
                type: "POST",
                url: "<?php echo admin_url(); ?>todo/create_todays_plan",
                data: {
                    task_ids: todaystodotaskid,
                    remove_task_ids: removetodotaskid,
                    huddlerSOD: true,
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    $('#mytodos-content').find(".dt-loader").remove();
                    if (response.status === 'success') {

                        alert_float("success", response.message);
                        //checkInUser();
                        console.log("Today plan submitted");
                        // window.location.reload();
                        setTimeout(function() {
                            window.location.href = '<?php echo admin_url(); ?>todo/mytodos';
                        }, 700);

                    } else {
                        alert_float("danger", response.message);
                    }

                },
                error: function(error) {
                    alert_float("danger", 'An error occurred while creating Today\'s Plan.');
                }
            });

        });
    });
    // Function to handle check-in process
    function checkInUser() {
        // Prepare the data you want to send
        var dataToSend = {
            staff_id: '<?php echo get_staff_user_id(); ?>',
            type_check: 1,
            edit_date: '',
            point_id: '',
            location_user: ''
        };

        // Send the data via AJAX to the check-in endpoint
        $.ajax({
            url: '<?php echo admin_url("timesheets/check_in_ts"); ?>',
            type: 'POST',
            data: dataToSend,
            success: function(response) {
                console.log("Check-in successful", response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("Check-in failed: " + textStatus, errorThrown);
            }
        });
    }
</script>

</html>