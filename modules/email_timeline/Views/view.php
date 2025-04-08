<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <h1><?php echo _l('email_timeline'); ?></h1>
                  
                  <!-- Search Form -->
                  <form action="<?php echo admin_url('email_timeline/retrieve_emails'); ?>" method="POST">
                     <div class="form-group">
                        <label for="search_criteria"><?php echo _l('search'); ?></label>
                        <input type="text" class="form-control" id="search_criteria" name="search_criteria" placeholder="Enter search criteria">
                     </div>
                     <button type="submit" class="btn btn-primary"><?php echo _l('search'); ?></button>
                  </form>
                  
                  <table class="table">
                     <thead>
                        <tr>
                           <th><?php echo _l('subject'); ?></th>
                           <th><?php echo _l('message'); ?></th>
                           <th><?php echo _l('type'); ?></th>
                           <th><?php echo _l('created_at'); ?></th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach ($emails as $email) { ?>
                        <tr>
                           <td><?php echo $email['subject']; ?></td>
                           <td><?php echo $email['message']; ?></td>
                           <td><?php echo _l($email['type']); ?></td>
                           <td><?php echo $email['created_at']; ?></td>
                           <?php $link = 'email_timeline/reply_email_view/'. $email['thread_id'];?>
                           <td><a href="<?php echo admin_url($link); ?>" value="" class="btn btn-sm btn-link btn-primary">Reply</a></td>
                        </tr>
                        <?php } ?>
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
