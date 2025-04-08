<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<table class="table table-bordered table-striped" id="questions" style="margin-top:0;">
    <thead style="background-color:#007bff; color: #fff;">
        <tr>
            <th>#</th>
            <th>Category</th>
            <th>Question Text</th>
            <th>Options</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($questions as $index => $question): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo isset($question['category']) && $question['category'] !== null ? htmlspecialchars($question['category']) : ''; ?></td>
                <td><?php echo isset($question['question_text']) && $question['question_text'] !== null ? htmlspecialchars($question['question_text']) : ''; ?></td>
                <td><?php echo isset($question['options']) && $question['options'] !== null ? htmlspecialchars($question['options']) : ''; ?></td>
                <td>
                    <input type="checkbox" name="questions[]" value="<?php echo $question['id']; ?>"
                        <?php echo isset($question['active']) && $question['active'] ? 'checked' : ''; ?>>
                </td>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>