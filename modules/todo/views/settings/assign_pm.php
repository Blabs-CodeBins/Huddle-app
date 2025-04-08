<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .form-group {
        margin-bottom: 0px;
    }


</style>
<table class="table table-bordered table-hover table-striped" id="assign-pm" style="width: 50%; margin-top:0;">
    <thead style="background-color:#007bff; color: #fff;">
        <tr>
            <th style="text-align: right; width:80px;">Emp ID</th>
            <th style="text-align: left;">Name</th>
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
        <?php foreach ($empMembers as $employee): ?>
            <tr>
                <td style="text-align: right;"><?= htmlspecialchars($employee['staffid'], ENT_QUOTES, 'UTF-8') ?></td>
                <td style="text-align: left;"><?= htmlspecialchars($employee['firstname'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <?php
                    $options = [
                        ['value' => '', 'text' => 'Select an option'],
                        ['value' => 'PM', 'text' => 'Project Manager'],
                        ['value' => 'TL', 'text' => 'Team Leader']
                    ];

                    $selected = isset($employee['todolist_pm']) && $employee['todolist_pm'] ? 'PM' : (isset($employee['toplinemanager']) && $employee['toplinemanager'] ? 'TL' : '');

                    echo render_select('assign_as[]', $options, ['value', 'text'], '', $selected, ['data-empId' => $employee['staffid']], [], '', 'form-control', false);
                    ?>

                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>