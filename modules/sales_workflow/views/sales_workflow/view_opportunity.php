
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h1>Opportunity Details</h1>
                        <p><strong>Stage:</strong> <?php //echo $opportunity->stage; ?></p>
                        <p><strong>Value:</strong> <?php //echo $opportunity->value; ?></p>
                        <p><strong>Close Date:</strong> <?php //echo $opportunity->close_date; ?></p>
                        <h2>Follow-ups</h2>
                        <table class="table table-border">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Follow-up Date</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($followups as $followup) : ?>
                                    <tr>
                                        <td><?php echo $followup['id']; ?></td>
                                        <td><?php echo $followup['followup_date']; ?></td>
                                        <td><?php echo $followup['status']; ?></td>
                                        <td><?php echo $followup['notes']; ?></td>
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