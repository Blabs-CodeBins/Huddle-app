<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-7 col-md-offset-2">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                    Compose Reply Email</h4>
                <?php echo form_open(admin_url('email_timeline/reply_email'), ['id' => 'reply_email_form']); ?>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php
                        if (isset($email)) {
                            $from_email = $email['sender'] ?? ''; // Assuming 'sender' is the key for the sender's email
                            $to_email = $email['recipient'] ?? '';
                            $cc = $email['cc'] ?? '';
                            $bcc = $email['bcc'] ?? '';
                            $sub = $email['subject'] ?? '';
                            $body = $email['message'] ?? '';
                        }
                        ?>

                        <div class="form-group" app-field-wrapper="from_email">
                            <label for="from_email" class="control-label"><small class="req text-danger">* </small>From</label>
                            <input type="text" id="from_email" name="from_email" class="form-control" value="<?php echo htmlspecialchars($from_email); ?>" autocomplete="off" placeholder="Enter Sender email">
                        </div>
                        <div class="form-group" app-field-wrapper="to_email">
                            <label for="to_email" class="control-label"><small class="req text-danger">* </small>To</label>
                            <input type="text" id="to_email" name="to_email" class="form-control" value="<?php echo htmlspecialchars($to_email); ?>" autocomplete="off" placeholder="Enter recipient email">
                        </div>
                        <?php echo render_input('cc', 'Cc', htmlspecialchars($cc), 'email', ['placeholder' => 'Enter CC email'], [], '', 'email-cc'); ?>
                        <?php echo render_input('bcc', 'Bcc', htmlspecialchars($bcc), 'email', ['placeholder' => 'Enter BCC email'], [], '', 'email-bcc'); ?>
                        <div class="form-group" app-field-wrapper="subject">
                            <label for="subject" class="control-label"><small class="req text-danger">* </small>Subject</label>
                            <input type="text" id="subject" name="subject" class="form-control" value="<?php echo htmlspecialchars($sub); ?>" autocomplete="off" placeholder="Enter subject">
                        </div>
                        <div class="form-group" app-field-wrapper="message">
                            <label for="message" class="control-label"><small class="req text-danger">* </small>Message</label>
                            <?php echo render_textarea('message', '', htmlspecialchars($body), ['required' => 'required'], [], '', 'email-message'); ?>
                        </div>
                        <div class="form-group">
                            <label for="attachments">Attachments</label>
                            <input type="file" id="attachments" name="attachments[]" class="form-control" multiple>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button class="btn btn-primary" type="submit">Send</button>
                        <button class="btn btn-default" type="button" onclick="window.location">Cancel</button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function() {
        init_editor('.email-message');
    });
</script>
</body>

</html>