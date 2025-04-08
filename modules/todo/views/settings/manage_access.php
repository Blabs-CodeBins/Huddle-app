<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<table class="table table-bordered table-hover table-striped" id="manage-access" style="width: 50%; margin-top:0;">
    <thead style="background-color:#007bff; color: #fff;">
        <tr>
            <th style="text-align: right; width:80px;">Emp ID</th>
            <?php if(!is_admin()): ?>
            <th style="text-align: left;">Name</th>
            <?php endif; ?>
            <th style="width: 250px;">Assign Manager</th>
        </tr>
    </thead>
    <tbody>
        <?php
        function sortByFirstname($a, $b)
        {
            return strcmp($a['firstname'], $b['firstname']);
        }
        usort($empMembers, 'sortByFirstname');
        usort($staffMembers, 'sortByFirstname');
        ?>
        <?php foreach ($empMembers as $employee):
            if ($employee['toplinemanager'] == 0) { ?>
                <tr>
                    <td style="text-align: right;"><?= htmlspecialchars($employee['staffid'], ENT_QUOTES, 'UTF-8') ?></td>
                    <?php if(!is_admin()): ?>
                    <td style="text-align: left;"><?= htmlspecialchars($employee['firstname'], ENT_QUOTES, 'UTF-8') ?></td>
                    <?php endif; ?>
                    <td>
                        <select name="reports_to" class="form-control selectpicker" id="assign_reports_to_<?= htmlspecialchars($employee['staffid'], ENT_QUOTES, 'UTF-8'); ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                            <option value="">Select</option>
                            <?php
                            foreach ($staffMembers as $key => $staff): ?>
                                <?php if ($staff['staffid'] !== $employee['staffid']): ?>
                                    <option value="<?= htmlspecialchars($staff['staffid'], ENT_QUOTES, 'UTF-8'); ?>" <?= ($staff['staffid'] == $employee['reports_to']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($staff['firstname'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <input type="hidden" name="employee_id" value="<?= $employee['staffid'] ?>">
                </tr>
        <?php
            }
        endforeach; ?>
    </tbody>
</table>