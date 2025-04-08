<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                <form method="post" action="<?php echo admin_url('sales_workflow/capture_lead'); ?>">
                    <input type="text" name="source" placeholder="Source">
                    <input type="text" name="type" placeholder="Type">
                    <button type="submit">Capture Lead</button>
                </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
</body>
</html>