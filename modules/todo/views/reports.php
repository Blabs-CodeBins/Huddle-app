<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .custom-table {
        width: 100%;
        max-width: 900px;
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

    table.custom-table.panel_s {
        margin-bottom: 0px;
    }

    .card-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: flex-start;
    }

    .card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        width: 220px;
        padding: 20px;
        text-align: center;
        transition: transform 0.2s;
    }

    .card-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: left;
    }

    .card-icon {
        font-size: 2.5em;
        margin-bottom: 15px;
        color: #007bff;
    }

    .card-title {
        font-size: 1.3em;
        margin-bottom: 10px;
        color: #333;
    }

    .card-value {
        font-size: 1.7em;
        color: #007bff;
        margin-bottom: 5px;
    }

    .card-ratio {
        font-size: 1.2em;
        color: #333;
    }

    .card-percentage {
        font-size: 0.9em;
        color: #777;
    }

    #mytodos-content .panel-group {
        display: flex;
        grid-gap: 20px;
    }

    #mytodos-content .panel.panel-default {
        width: calc(50% / 3);
        padding: 5px;
        margin-top: 0px !important;
        background-color: rgb(240, 247, 255);
        border-color: rgb(186, 200, 217);
    }

    .icon-img {
        font-size: 2.5em;
    }

    .title {
        font-size: 16px;
    }
</style>
<div id="wrapper">
    <div class="content" id="mytodos-content">
        <?php if($isProjectManager || $istoplinemanager || is_admin($staffid)){ ?>
        <div class="row">
            <div class="col-md-12 tw-flex tw-justify-between tw-mb-3 tw-pl-2">
                <div class="col-md-4">
                    <div class="form-group">
                        <h4 class="tw-font-semibold tw-text-gray-800 tw-m-0 col-sm-4 tw-pl-2">Get Reports of:</h4>
                        <div class="col-sm-8">
                            <?php
                            $currentUserId = $this->session->userdata('staff_user_id');
                            $options = [];
                            $options[] = [
                                'staffid' => $staffid,
                                'full_name' => 'My team and I'

                            ];
                            foreach ($reportingUsers as $user) {
                                $options[] = [
                                    'staffid' => $user['staffid'],
                                    'full_name' => $user['firstname'] . ' ' . $user['lastname']
                                ];
                            }
                            echo render_select('reporting_user', $options, ['staffid', 'full_name'], '', '', [], [], '', 'form-control', false);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="exprt-button col-md-7">
                    <form method="post" action="<?php echo admin_url() ?>todo/exportcsv">
                        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                        <button type="submit" class="btn btn-primary btn-sm export-btn tw-ml-4">Export CSV</button>
                    </form>
                </div>
            </div>
            <hr>
        </div>
        <?php } ?>
        <div class="row">
            <!-- Quantitative Report Section -->
            <div class="col-md-12 tw-mb-8">
                <div class="heading-title tw-mb-2 tw-pl-2">
                    <h3 class="tw-font-semibold tw-text-gray-800 tw-m-0 tw-pl-2">
                        Quantitative
                    </h3>
                </div>
                <div class="col-md-12 point-table">
                    <table class="custom-table panel_s">
                        <tbody>
                            <tr>
                                <td>
                                    <span class="tw-text-gray-600 text-right">
                                        <i class="fas fa-calendar-day icon"></i>Points Assigned Today
                                    </span>
                                    <h3 id="points-today" class="tw-font-semibold tw-text-gray-800 tw-m-0 text-right"><?php echo $QuantitativeReport['Points_Assigned_Today']; ?></h3>
                                </td>
                                <td>
                                    <span class="tw-text-gray-600 text-right">
                                        <i class="fas fa-calendar-alt icon green"></i>Earned in <?php echo date('F'); ?>
                                    </span>
                                    <h3 id="points-month" class="tw-font-semibold tw-text-gray-800 tw-m-0 text-right"><?php echo $QuantitativeReport['Earned_This_Month']; ?></h3>
                                </td>
                                <td>
                                    <span class="tw-text-gray-600 text-right">
                                        <i class="fas fa-calendar-check icon yellow"></i>Earned this Year
                                    </span>
                                    <h3 id="points-year" class="tw-font-semibold tw-text-gray-800 tw-m-0 text-right"><?php echo $QuantitativeReport['Earned_This_Year']; ?></h3>
                                </td>
                                <td>
                                    <span class="tw-text-gray-600 text-right">
                                        <i class="fas fa-exclamation-triangle icon red"></i>Points Due
                                    </span>
                                    <h3 id="points-due" class="tw-font-semibold tw-text-gray-800 tw-m-0 text-right"><?php echo $QuantitativeReport['Points_Due']; ?></h3>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Qualitative Report Section -->
            <div class="col-md-12 tw-mt-8 tw-pl-4">
                <div class="heading-title tw-mb-2 tw-pl-2">
                    <h3 class="tw-font-semibold tw-text-gray-800 tw-m-0 tw-pl-2">
                        Qualitative
                    </h3>
                </div>
                <div class="col-md-12 point-table">
                    <div class="card-container">
                        <div id="ontime" class="card" style="width:285px;display:flex;align-items:center">
                            <div class="card-icon" style="margin-right:15px">⏰</div>
                            <div class="card-content">
                                <div class="card-title">On Time</div>
                                <div class="card-value"><?= $QualitativeReport['On_Time']['ratio'] ?></div>
                                <div class="card-percentage">(<?= $QualitativeReport['On_Time']['percentage'] ?>)</div>
                            </div>
                        </div>

                        <div id="reasonable-delay" class="card" style="width:285px;display:flex;align-items:center">
                            <div class="card-icon" style="margin-right:15px">⏲️</div>
                            <div class="card-content">
                                <div class="card-title">Reasonable Delay</div>
                                <div class="card-value"><?= $QualitativeReport['Reasonable_Delay']['ratio'] ?></div>
                                <div class="card-percentage">(<?= $QualitativeReport['Reasonable_Delay']['percentage'] ?>)</div>
                            </div>
                        </div>

                        <div id="delay" class="card" style="width:285px;display:flex;align-items:center">
                            <div class="card-icon" style="margin-right:15px">🚫</div>
                            <div class="card-content">
                                <div class="card-title">Delayed</div>
                                <div class="card-value"><?= $QualitativeReport['Delay']['ratio'] ?></div>
                                <div class="card-percentage">(<?= $QualitativeReport['Delay']['percentage'] ?>)</div>
                            </div>
                        </div>
                        <div class="card" style="width: 285px;display: flex;align-items: center;">
                            <div class="card-icon" style="margin-right: 15px;">👨‍💻</div>
                            <div class="card-content">
                                <div class="card-title">Top Quality Code</div>
                                <div class="card-value">Coming Soon...</div>
                                <div class="card-percentage"></div>
                            </div>
                        </div>
                        <div class="card" style="width: 285px;display: flex;align-items: center;">
                            <div class="card-icon" style="margin-right: 15px;">📝</div>
                            <div class="card-content">
                                <div class="card-title">Client Complain</div>
                                <div class="card-value">Coming Soon...</div>
                                <div class="card-percentage"></div>
                            </div>
                        </div>
                        <div class="card" style="width: 285px;display: flex;align-items: center;">
                            <div class="card-icon" style="margin-right: 15px;">🌟</div>
                            <div class="card-content">
                                <div class="card-title">Client Reviews</div>
                                <div class="card-value">Coming Soon...</div>
                                <div class="card-percentage"></div>
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
        $('#reporting_user').on('change', function() {
            var selectedValue = $(this).val();
            console.log(selectedValue);
            $.ajax({
                type: "POST",
                url: "<?php echo admin_url(); ?>todo/getReports/" + selectedValue,
                dataType: "json",
                success: function(response) {
                    if (response.status === 'success') {
                        updateReports(response.data);
                    } else {
                        console.error("Failed to fetch data");
                    }
                }
            });
        });
    });

    function updateReports(data) {
        // Update Quantitative Data
        const quant = data.QuantitativeReport;
        $("#points-today").text(quant.Points_Assigned_Today);
        $("#points-month").text(quant.Earned_This_Month);
        $("#points-year").text(quant.Earned_This_Year);
        $("#points-due").text(quant.Points_Due);

        // Update Qualitative Data
        const qual = data.QualitativeReport;
        updateCard("#ontime", qual.On_Time);
        updateCard("#delay", qual.Delay);
        updateCard("#reasonable-delay", qual.Reasonable_Delay);
    }

    function updateCard(selector, report) {
        $(`${selector} .card-value`).text(report.ratio);
        $(`${selector} .card-percentage`).text(`(${report.percentage})`);
    }
</script>

</html>