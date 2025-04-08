<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div id="create-todo-container" class="container">
        <h1 id="create-todo-header" class="my-4">Create Todo</h1>

        <!-- Display flash message -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success">
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <?php 
        if ($source === 'my_todos'){
            $url = '/savetodo?source=mytodo';
        }else{
            $url = '/savetodo';
        }
        ?>

        <form action="<?php echo admin_url('todo') . $url; ?>" method="post">
            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

            <!-- Hidden field for todoid if updating existing todo -->
            <?php if (isset($todoid)): ?>
                <input type="hidden" name="todoid" value="<?php echo htmlspecialchars($todoid); ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="assigned_to">Assigned To:</label>
                <select id="assigned_to" name="assigned_to" class="form-control">
                    <?php if ($source === 'my_todos'): ?>
                        <option value="<?= htmlspecialchars($loggedInUser->staffid) ?>" selected>
                            <?= htmlspecialchars($loggedInUser->firstname . ' ' . $loggedInUser->lastname) ?>
                        </option>
                    <?php else: ?>
                        <?php foreach ($members as $member): ?>
                            <option value="<?= htmlspecialchars($member['value']) ?>">
                                <?= htmlspecialchars($member['text']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Todo Description</label>
                <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" name="due_date" id="due_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Pending">Pending</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>

            <!-- Hidden field for 'finished' status -->
            <input type="hidden" name="finished" value="0">

            <!-- Date finished field, optional -->
            <!-- <div class="form-group">
                <label for="datefinished">Date Finished (optional)</label>
                <input type="date" name="datefinished" id="datefinished" class="form-control">
            </div> -->

            <!-- Item order field, if needed -->
            <div class="form-group">
                <label for="item_order">Item Order (optional)</label>
                <input type="number" name="item_order" id="item_order" class="form-control">
            </div>

            <button type="submit" id="save-todo-button" class="btn btn-primary">Save Todo</button>
        </form>
    </div>
</div>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<?php init_tail(); ?>
</body>
</html>
