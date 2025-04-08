
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3><b><?php echo $title; ?></b></h3>
                        <h4>Leads</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Source</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($leads as $lead) : ?>
                                    <tr>
                                        <td><?php echo $lead['id']; ?></td>
                                        <td><?php echo $lead['source']; ?></td>
                                        <td><?php echo $lead['type']; ?></td>
                                        <td><?php echo $lead['status']; ?></td>
                                        <td><?php echo $lead['created_at']; ?></td>
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