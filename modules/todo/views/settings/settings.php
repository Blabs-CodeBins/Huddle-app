<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php echo form_open_multipart($this->uri->uri_string() . '?group=' . $current_group, ['id' => 'huddler-settings-form']); ?>
        <div class="row">
            <?php if ($this->session->flashdata('debug')) {
            ?>
                <div class="col-lg-12">
                    <div class="alert alert-warning">
                        <?php echo $this->session->flashdata('debug'); ?>
                    </div>
                </div>
            <?php
            } ?>
            <div class="col-md-3">
                <h4 class="tw-font-semibold tw-mt-0 tw-text-neutral-800">
                    Settings
                </h4>
                <ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked">
                    <li class="settings-group-manage_access <?php echo ($current_group === 'manage_access') ? 'active' : ''; ?>">
                        <a href="<?= admin_url('todo/settings?group=manage_access'); ?>" data-group="manage_access">
                            <i class="fa fa-users menu-icon"></i> Manage Access
                        </a>
                    </li>
                    <li class="settings-group-assign_pm <?php echo ($current_group === 'assign_pm') ? 'active' : ''; ?>">
                        <a href="<?= admin_url('todo/settings?group=assign_pm'); ?>" data-group="assign_pm">
                            <i class="fa fa-users menu-icon"></i> Assign PM / TL
                        </a>
                    </li>
                    <li class="settings-group-questions <?php echo ($current_group === 'questions') ? 'active' : ''; ?>">
                        <a href="<?= admin_url('todo/settings?group=questions'); ?>" data-group="questions">
                            <i class="fa fa-cogs menu-icon"></i> Questions
                        </a>
                    </li>
                </ul>
            </div>
            <div class="btn-bottom-toolbar text-right">
                <button type="submit" class="btn btn-primary">
                    Save Settings
                </button>
            </div>
            <div class="col-md-9">
                <h4 class="tw-font-semibold tw-mt-0 tw-text-neutral-800">
                    <?php echo $title; ?>
                </h4>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php hooks()->do_action('before_settings_group_view', $current_group); ?>
                        <?php $this->load->view($current_group) ?>
                        <?php hooks()->do_action('after_settings_group_view', $current_group); ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php echo form_close(); ?>
        <div class="btn-bottom-pusher"></div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    const groupUpdates = '<?= $_GET['group'] ?? 'manage_access'; ?>';
    let updates = [];

    $(document).ready(function () {
        $('#huddler-settings-form').submit(function (e) {
            e.preventDefault(); // Prevent default form submission
            updates = []; // Clear the updates array to avoid duplicates

            if (groupUpdates === 'manage_access') {
                $('#manage-access select[name="reports_to"]').each(function () {
                    const employeeId = $(this).attr('id').replace('assign_reports_to_', '');
                    let managerId = $(this).val();
                    managerId = managerId === "" ? null : managerId; // Convert empty value to null

                    updates.push({
                        employee_id: employeeId,
                        manager_id: managerId
                    });
                });
            }

            if (groupUpdates === 'assign_pm') {
                $('#assign-pm select[name="assign_as[]"]').each(function () {
                    const employeeId = $(this).data('empid');
                    if (employeeId !== undefined) {
                        let assign_as = $(this).val();
                        assign_as = assign_as === "" ? null : assign_as;

                        updates.push({
                            employee_id: employeeId,
                            assign_as: assign_as
                        });
                    } else {
                        console.error('Employee ID is undefined');
                    }
                });
            }

            if (groupUpdates === 'questions') {
                $('#questions input[name="questions[]"]:checked').each(function () {
                    updates.push($(this).val()); // Collect selected question IDs
                });
            }

            console.log(updates); // Debugging output

            // Send AJAX request
            $.ajax({
                url: '<?php echo admin_url() ?>todo/manage_access',
                type: 'POST',
                data: {
                    updates: updates,
                    group: groupUpdates,
                    '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert_float('success', 'All changes have been saved successfully.');
                    } else {
                        alert_float('danger', 'An error occurred while saving changes.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', error);
                    alert_float('danger', 'An unexpected error occurred.');
                }
            });
        });
    });
</script>


<?php hooks()->do_action('settings_group_end', $current_group); ?>
</body>

</html>