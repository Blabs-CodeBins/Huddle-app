<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3><b>FollowUp Report</b></h3>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Task</th>
                                    <th>Assigned To</th>
                                    <th>Due Date</th>
                                    <th>Completed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tasks as $task) : ?>
                                    <tr>
                                        <td><?php echo $task['id']; ?></td>
                                        <td><?php echo $task['task']; ?></td>
                                        <td><?php echo $task['assigned_to']; ?></td>
                                        <td><?php echo $task['due_date']; ?></td>
                                        <td><?php echo $task['completed'] ? 'Yes' : 'No'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>

</html>