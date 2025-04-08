<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
   .panel-default>.panel-heading {
      background-color: rgb(240, 247, 255);
   }

   .panel-default {
      border-color: rgb(186, 200, 217);
   }

   .panel-group .panel {
      margin-bottom: 12px;
      box-shadow: none;
   }

   .panel-title .subject,
   .panel-title .timeline-date,
   .panel-title .fa-eye {
      font-weight: 400;
   }

   .tw-my-3.sender-name,
   .text-left.account-name {
      font-size: 16px;
      font-weight: 600;
   }

   .scrollable-container {
      max-height: 820px;
      min-height: 820px;
      overflow-y: auto;
   }

   .sticky-heading {
      position: sticky;
      top: -24px;
      background: white;
      z-index: 35;
      padding-top: 10px;
   }

   .list-group-item:hover,
   .list-group-item.active:hover {
      background-color: #f5f5f5;
      border-color: #f5f5f5;
   }

   .list-group-item.active {
      background-color: rgb(240, 247, 255);
      border-color: rgb(186, 200, 217);
   }

   .list-group-item a {
      display: block;
      color: #333;
      text-decoration: none;
   }

   .list-group-item a:hover {
      text-decoration: none;
      color: #333;
   }

   ul.list-unstyled .media:first-child {
      margin-top: 15px;
   }

   #timeline-content {
      padding-bottom: 0px;
   }

   .list-group-item {
      border-left: none;
      border-right: none;
      border-radius: 0 !important;
   }

   .custom-date-time {
      display: flex;
      align-items: center;
      text-align: center;
      width: 100%;
      margin: 20px 0;
   }

   .custom-date-time::before,
   .custom-date-time::after {
      content: '';
      flex: 1;
      border-bottom: 1px solid #ccc;
   }

   .custom-date-time::before {
      margin-right: .25em;
   }

   .custom-date-time::after {
      margin-left: .25em;
   }

   .middle-sec {
      width: 80%;
   }

   /* Sidebar and responsiveness */
   #account-sidebar {
      position: fixed;
      top: 0px;
      right: -436px;
      width: 425px;
      height: 100%;
      background-color: #f8f9fa;
      transition: right 0.3s ease-in-out;
      z-index: 999;
      padding: 0px;
   }

   #account-sidebar.open {
      right: 0;
   }

   #account-sidebar-close-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      cursor: pointer;
      z-index: 99;
      font-size: 20px;
   }

   .email-iframe {
      width: 100%;
      min-height: 496px;
      height: 100%;
      border: none;
      overflow: hidden;
   }

   @media (max-width: 768px) {

      .content .row>.col-md-3,
      .content .row>.col-md-6 {
         display: none;
      }

      .scrollable-container {
         max-height: 100%;
         min-height: 900px;
      }
   }

   @media (min-width: 769px) {

      .content .row>.col-md-3,
      .content .row>.col-md-6 {
         display: block;
      }
   }

   /* Sticky sub-header for mobile view */
   .sticky-sub-header {
      position: sticky;
      top: 0;
      z-index: 44;
      background-color: #fff;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: space-around;
      padding: 10px 0;
      margin-bottom: 20px;
   }

   .sticky-sub-header .tab {
      flex: 1;
      text-align: center;
      cursor: pointer;
      padding: 10px;
   }

   .sticky-sub-header .tab.active {
      font-weight: bold;
      border-bottom: 2px solid #007bff;
   }

   .single-line-ellipsis {
      white-space: nowrap;
      /* Prevent the text from wrapping to the next line */
      overflow: hidden;
      /* Hide the overflow content */
      text-overflow: ellipsis;
      /* Add ellipsis (...) at the end of the line */
   }

   /* Default styles for screens wider than 1024px */
   @media (min-width: 1025px) {
      #desktop-account {
         width: 25%;
      }

      #desktop-timeline {
         width: 50%;
      }

      #desktop-accountinfo {
         width: 25%;
         display: block;
      }

      #account-info-toggle {
         display: none;
      }
   }

   /* Styles for screens smaller than 1024px */
   @media (max-width: 1024px) {
      #desktop-account {
         width: 35%;
      }

      #desktop-timeline {
         width: 65%;
      }

      #desktop-accountinfo {
         display: none;
      }

      #account-info-toggle {
         display: block;
      }

      .scrollable-container {
         max-height: 900px;
         min-height: 900px;
      }
   }

   /* For screens between 1024px and 520px */
   @media (max-width: 1024px) and (min-width: 520px) {
      .middle-sec {
         width: 70%;
      }
   }

   /* For screens below 520px */
   @media (max-width: 520px) {
      .middle-sec {
         width: 60%;
      }
   }

   @media (min-width: 769px) {

      .sticky-sub-header,
      .row.mobile-view {
         display: none;
      }
   }

   /* Hide all sections by default */
   .section-content {
      display: none;
   }

   .hideable {
      display: none;
   }

   .email-text {
      white-space: pre-wrap;
      /* Make sure text wraps within the container */
      word-wrap: break-word;
      /* Handle long words or URLs */
      background: none;
      /* Remove default background */
      border: none;
      /* Remove default border */
      padding: 0;
      /* Remove default padding */
      font-family: inherit;
      /* Use the same font as the surrounding text */
      font-size: inherit;
      /* Use the same font size as the surrounding text */
      color: inherit;
      /* Use the same text color as the surrounding text */
   }

   .form-horizontal .form-group {
      margin-left: 0px;
      margin-right: 0px;
   }

   .dropdown-menu.reply-dropdown {
      top: -20px;
      left: -100px;
   }
   i.fas{
    cursor: pointer;
   }
</style>
<div id="wrapper">
   <div class="content" id="timeline-content">
      <!-- Sticky sub-header for mobile view -->
      <div class="sticky-sub-header">
         <div class="tab active" id="tab-account">Account</div>
         <div class="tab" id="tab-timeline">Timeline</div>
         <div class="tab" id="tab-accountinfo">Account Info</div>
      </div>
      <div class="row desktop-view">
         <div class="col-md-3" id="desktop-account">
            <div class="panel_s">
               <div class="panel-body scrollable-container">
                  <div class="row">
                     <div class="page-header sticky-heading tw-m-0">
                        <h4><b>Accounts</b></h4>
                     </div>
                     <div id="emptyState" class="alert alert-info text-center tw-mt-5 tw-p-3" style="display:none">No accounts have been added yet.</div>
                     <div id="accountCount" class="alert alert-info text-center tw-mt-5 tw-p-3">2311 accounts by last interaction (desc.)</div>
                     <ul class="list-group" id="accountList">
                        <li class="list-group-item active">
                           <a href="#" class="text-muted">
                              <div class="row">
                                 <div class="col-xs-2">
                                    <img src="https://img.fullcontact.com/static/dfa0ab6849d409d61e802c732b634255_feb2b5df6e5f5443c08c36d344e559a7b643f145eaaa6c46ccd06ed5efd1f9cb" class="img-circle staff-profile-image-small" alt="Account Avatar">
                                 </div>
                                 <div class="col-xs-10">
                                    <div class="tw-flex tw-justify-between">
                                       <p class="text-left account-name">Test1</p>
                                       <p class="text-right">18 Jul</p>
                                    </div>
                                    <p class="text-left single-line-ellipsis"><span class="glyphicon glyphicon-envelope"></span> Vikash1 - Inquiry About Your WordPress Development Services</p>
                                 </div>
                              </div>
                           </a>
                        </li>
                        <li class="list-group-item">
                           <a href="#" class="text-muted">
                              <div class="row">
                                 <div class="col-xs-2">
                                    <img src="https://lib.salesflare.com/avatars/avatar_company_default_2.png" class="img-circle staff-profile-image-small" alt="Account Avatar">
                                 </div>
                                 <div class="col-xs-10">
                                    <div class="tw-flex tw-justify-between">
                                       <p class="text-left account-name">Hung Than</p>
                                       <p class="text-right">3:35 PM</p>
                                    </div>
                                    <p class="text-left single-line-ellipsis"><span class="glyphicon glyphicon-envelope"></span> Userback - [HWG - Testing] New Bug created by Anuradha Mattaparthi</p>
                                 </div>
                              </div>
                           </a>
                        </li>
                        <li class="list-group-item">
                           <a href="#" class="text-muted">
                              <div class="row">
                                 <div class="col-xs-2">
                                    <img src="https://logo.clearbit.com/nwida.org" class="img-circle staff-profile-image-small" alt="Account Avatar">
                                 </div>
                                 <div class="col-xs-10">
                                    <div class="tw-flex tw-justify-between">
                                       <p class="text-left account-name">NWIDA</p>
                                       <p class="text-right">1:53 PM</p>
                                    </div>
                                    <p class="text-left single-line-ellipsis"><span class="glyphicon glyphicon-envelope"></span> Adam Wolf - The latest news from NWIDA</p>
                                 </div>
                              </div>
                           </a>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-6" id="desktop-timeline">
            <div class="panel_s">
               <div class="panel-body scrollable-container">
                  <div class="page-header sticky-heading tw-m-0">
                     <h4><b><?php echo _l('email_timeline'); ?></b></h4>
                  </div>
                  <?php
                  // Group emails by date
                  $groupedEmails = [];
                  foreach ($emails as $email) {
                     $date = (new DateTime($email['created_at']))->format('Y-m-d');
                     if (!isset($groupedEmails[$date])) {
                        $groupedEmails[$date] = [];
                     }
                     $groupedEmails[$date][] = $email;
                  }

                  foreach ($groupedEmails as $date => $emails) :
                     $formattedDate = (new DateTime($date))->format('l, F j, Y');
                  ?>
                     <div class="main-section">
                        <div class="dateandtime text-center sticky-heading tw-my-3">
                           <div class="custom-date-time">
                              <?php echo $formattedDate; ?>
                           </div>
                        </div>
                        <div class="panel-group" id="accordion-<?php echo $date; ?>" role="tablist" aria-multiselectable="true">
                           <?php foreach ($emails as $email) : ?>
                              <div class="panel panel-default">
                                 <div class="panel-heading" role="tab" id="heading<?php echo $email['id']; ?>">
                                    <h4 class="panel-title">
                                       <div role="button" data-toggle="collapse" href="#collapse<?php echo $email['id']; ?>" aria-expanded="true" aria-controls="collapse<?php echo $email['id']; ?>">
                                          <div class="tw-flex tw-justify-between">
                                             <div class="profile">
                                                <img src="https://localhost/pmpdev1/assets/images/user-placeholder.jpg" alt="Profile Picture" class="img img-responsive staff-profile-image-small tw-ring-1 tw-ring-offset-2 tw-ring-primary-500 tw-mx-1 tw-my-3">
                                             </div>
                                             <div class="middle-sec">
                                                <div class="tw-my-3 sender-name"><?php echo $email['sender_name']; ?></div>
                                                <div class="tw-my-2 subject single-line-ellipsis"><?php echo $email['subject']; ?></div>
                                             </div>
                                             <div class="icons tw-my-3 text-right">
                                                <i class="fas fa-eye"></i>
                                                <span class="timeline-date">04:49</span>
                                             </div>
                                          </div>
                                       </div>
                                    </h4>
                                 </div>
                                 <div id="collapse<?php echo $email['id']; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?php echo $email['id']; ?>">
                                    <div class="panel-body">
                                       <div class="tw-flex tw-justify-between">
                                          <div class="heading-left">
                                             <strong>From:</strong> <?php echo $email['sender']; ?><br>
                                             <strong>To:</strong> <?php echo $email['recipient']; ?><br>
                                          </div>
                                          <div class="heading-right">
                                             <span class="float-right dropdown">
                                                <i class="fas fa-reply tw-p-2 dropdown-toggle" data-toggle="dropdown"></i> <!-- Reply icon -->
                                                <ul class="dropdown-menu reply-dropdown">
                                                   <li><a data-toggle="modal" href='#modal-id'><i class="fa fa-reply" aria-hidden="true"></i> Reply</a></li>
                                                   <li><a href="#"><i class="fa fa-reply-all" aria-hidden="true"></i> Reply All</a></li>
                                                   <li><a href="#"><i class="fa fa-share" aria-hidden="true"></i> Forward</a></li>
                                                </ul>
                                                <i class="fas fa-trash tw-pl-2"></i> <!-- Delete icon -->
                                             </span>
                                          </div>
                                       </div>
                                       <hr>
                                       <!-- <?php //echo nl2br(trim($email['message'])); 
                                             ?> -->
                                       <?php if ($email['is_html']) : ?>
                                          <iframe id="email-content-<?php echo $email['id']; ?>" class="email-iframe" sandbox="allow-same-origin allow-popups allow-popups-to-escape-sandbox" frameborder="0"></iframe>
                                          <script>
                                             document.addEventListener('DOMContentLoaded', function() {
                                                var iframe = document.getElementById('email-content-<?php echo $email['id']; ?>');
                                                var doc = iframe.contentDocument || iframe.contentWindow.document;
                                                doc.open();
                                                doc.write(`<?php echo addslashes(htmlspecialchars_decode($email['message'], ENT_QUOTES)); ?>`);
                                                doc.close();

                                                // Adjust iframe height to content
                                                iframe.onload = function() {
                                                   iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';
                                                   iframe.style.width = '100%';
                                                };
                                             });
                                          </script>
                                       <?php else : ?>
                                          <pre class="email-text"><?php echo $email['message']; ?></pre>
                                       <?php endif; ?>
                                    </div>
                                 </div>
                              </div>
                           <?php endforeach; ?>
                        </div>
                     </div>
                  <?php endforeach; ?>
               </div>
            </div>
         </div>
         <div class="col-md-3" id="desktop-accountinfo">
            <div class="row">
               <div class="col-md-12">
                  <div class="panel_s">
                     <div class="panel-body scrollable-container">
                        <div class="row">
                           <div class="page-header sticky-heading tw-m-0">
                              <h4><b>Account Information</b></h4>
                           </div>
                           <div class="col-xs-12">
                              <ul class="list-unstyled">
                                 <li class="media">
                                    <div class="media-left">
                                       <i class="glyphicon glyphicon-globe"></i>
                                    </div>
                                    <div class="media-body">
                                       <a href="https://www.rohyderabad.com" target="_blank">https://www.rohyderabad.com</a>
                                    </div>
                                 </li>
                                 <li class="media">
                                    <div class="media-left">
                                       <i class="glyphicon glyphicon-info-sign"></i>
                                    </div>
                                    <div class="media-body">
                                       <p class="text-truncate">Test descriptions</p>
                                    </div>
                                 </li>
                                 <li class="media">
                                    <div class="media-left">
                                       <i class="glyphicon glyphicon-user"></i>
                                    </div>
                                    <div class="media-body">
                                       <span>12</span>
                                    </div>
                                 </li>
                                 <li class="media hideable">
                                    <div class="media-left">
                                       <i class="glyphicon glyphicon-envelope"></i>
                                    </div>
                                    <div class="media-body">
                                       <ul class="list-unstyled">
                                          <li><a href="mailto:test@gmail.com">test@gmail.com</a></li>
                                       </ul>
                                    </div>
                                 </li>
                                 <li class="media hideable">
                                    <div class="media-left">
                                       <i class="glyphicon glyphicon-phone"></i>
                                    </div>
                                    <div class="media-body">
                                       <ul class="list-unstyled">
                                          <li><a href="tel:09064529941">09064529941</a></li>
                                       </ul>
                                    </div>
                                 </li>
                                 <li class="media hideable">
                                    <div class="media-left">
                                       <i class="glyphicon glyphicon-map-marker"></i>
                                    </div>
                                    <div class="media-body">
                                       <ul class="list-unstyled">
                                          <li>Ground Floor, Hatch Station, 500003 Hyderabad, Telangana, India</li>
                                       </ul>
                                    </div>
                                 </li>
                                 <li class="media hideable">
                                    <div class="media-left">
                                       <i class="glyphicon glyphicon-cog"></i>
                                    </div>
                                    <div class="media-body">
                                       <ul class="list-unstyled">
                                          <li>Lead Status Tag: BD chat, New Lead</li>
                                          <li>Lead/Client Type: BD - Lead</li>
                                          <li>Client Level: Unknown</li>
                                          <li>Source: BD Growth Suite</li>
                                       </ul>
                                    </div>
                                 </li>
                                 <li class="media hideable" ng-if="account.tags.length > 0">
                                    <div class="media-left">
                                       <i class="glyphicon glyphicon-tag"></i>
                                    </div>
                                    <div class="media-body">
                                       <ul class="list-inline">
                                          <li><span class="label label-default">Test</span></li>
                                       </ul>
                                    </div>
                                 </li>
                              </ul>
                           </div>
                           <div class="col-xs-12">
                              <div class="btn-group pull-right">
                                 <button class="btn btn-link btn-sm" type="button">Edit</button>
                                 <button class="btn btn-link btn-sm show-more" type="button">Show more</button>
                                 <button class="btn btn-link btn-sm show-less" type="button" style="display:none;">Show less</button>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="page-header sticky-heading">
                              <h4><b>Contacts</b></h4>
                           </div>
                           <div id="emptyState" class="alert alert-info text-center" style="display:none">No contacts have been added yet.</div>
                           <ul class="list-group" id="contactList">
                              <li class="list-group-item">
                                 <a href="#" class="text-muted">
                                    <div class="row">
                                       <div class="col-xs-2">
                                          <img src="https://lib.salesflare.com/avatars/avatar_tile_p_80.png" class="img-circle staff-profile-image-small" alt="Contact Avatar">
                                       </div>
                                       <div class="col-xs-8">
                                          <p class="text-left"><strong>P 1</strong></p>
                                       </div>
                                       <div class="col-xs-2 text-right">
                                          <button class="btn btn-default btn-xs" aria-label="Email P 1"><span class="glyphicon glyphicon-envelope"></span></button>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li class="list-group-item">
                                 <a href="#" class="text-muted">
                                    <div class="row">
                                       <div class="col-xs-2">
                                          <img src="https://lib.salesflare.com/avatars/avatar_tile_p_80.png" class="img-circle staff-profile-image-small" alt="Contact Avatar">
                                       </div>
                                       <div class="col-xs-8">
                                          <p class="text-left"><strong>P 2</strong></p>
                                       </div>
                                       <div class="col-xs-2 text-right">
                                          <button class="btn btn-default btn-xs" aria-label="Email P 2"><span class="glyphicon glyphicon-envelope"></span></button>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li class="list-group-item">
                                 <a href="#" class="text-muted">
                                    <div class="row">
                                       <div class="col-xs-2">
                                          <img src="https://lib.salesflare.com/avatars/avatar_tile_t_80.png" class="img-circle staff-profile-image-small" alt="Contact Avatar">
                                       </div>
                                       <div class="col-xs-8">
                                          <p class="text-left"><strong>TestBlabs</strong></p>
                                       </div>
                                       <div class="col-xs-2 text-right">
                                          <button class="btn btn-default btn-xs" aria-label="Email TestBlabs"><span class="glyphicon glyphicon-envelope"></span></button>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li class="list-group-item">
                                 <a href="#" class="text-muted">
                                    <div class="row">
                                       <div class="col-xs-2">
                                          <img src="https://lib.salesflare.com/avatars/avatar_tile_v_80.png" class="img-circle staff-profile-image-small" alt="Contact Avatar">
                                       </div>
                                       <div class="col-xs-8">
                                          <p class="text-left"><strong>Vikash</strong></p>
                                       </div>
                                       <div class="col-xs-2 text-right">
                                          <button class="btn btn-default btn-xs" aria-label="Email Vikash"><span class="glyphicon glyphicon-envelope"></span></button>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li class="list-group-item">
                                 <a href="#" class="text-muted">
                                    <div class="row">
                                       <div class="col-xs-2">
                                          <img src="https://lib.salesflare.com/avatars/avatar_tile_v_80.png" class="img-circle staff-profile-image-small" alt="Contact Avatar">
                                       </div>
                                       <div class="col-xs-8">
                                          <p class="text-left"><strong>Vikash1</strong></p>
                                       </div>
                                       <div class="col-xs-2 text-right">
                                          <button class="btn btn-default btn-xs" aria-label="Email Vikash1"><span class="glyphicon glyphicon-envelope"></span></button>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                           </ul>
                           <div class="text-right">
                              <button class="btn btn-link btn-sm" id="manageContacts" aria-label="Manage Contacts">Manage</button>
                           </div>
                        </div>
                        <div class="row">
                           <div class="page-header sticky-heading">
                              <h4><b>Team</b></h4>
                           </div>
                           <div id="emptyState" class="alert alert-info text-center" style="display:none">No team members have been added yet.</div>
                           <ul class="list-group" id="teamList">
                              <li class="list-group-item">
                                 <a href="#" class="text-muted">
                                    <div class="row">
                                       <div class="col-xs-2">
                                          <img src="https://lib.salesflare.com/avatars/avatar_tile_n_80.png" class="img-circle staff-profile-image-small" alt="Team Member Avatar">
                                       </div>
                                       <div class="col-xs-8">
                                          <p class="text-left"><strong>Nasiruddin Saikh</strong></p>
                                       </div>
                                       <div class="col-xs-2 text-right">
                                          <button class="btn btn-default btn-xs" aria-label="Email Nasiruddin Saikh"><span class="glyphicon glyphicon-envelope"></span></button>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li class="list-group-item">
                                 <a href="#" class="text-muted">
                                    <div class="row">
                                       <div class="col-xs-2">
                                          <img src="https://lib.salesflare.com/avatars/avatar_tile_y_80.png" class="img-circle staff-profile-image-small" alt="Team Member Avatar">
                                       </div>
                                       <div class="col-xs-8">
                                          <p class="text-left"><strong>Yakin Shah</strong></p>
                                       </div>
                                       <div class="col-xs-2 text-right">
                                          <button class="btn btn-default btn-xs" aria-label="Email Yakin Shah"><span class="glyphicon glyphicon-envelope"></span></button>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li class="list-group-item">
                                 <a href="#" class="text-muted">
                                    <div class="row">
                                       <div class="col-xs-2">
                                          <img src="https://lib.salesflare.com/avatars/avatar_tile_t_80.png" class="img-circle staff-profile-image-small" alt="Team Member Avatar">
                                       </div>
                                       <div class="col-xs-8">
                                          <p class="text-left"><strong>TestBlabs</strong></p>
                                       </div>
                                       <div class="col-xs-2 text-right">
                                          <button class="btn btn-default btn-xs" aria-label="Email TestBlabs"><span class="glyphicon glyphicon-envelope"></span></button>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                           </ul>
                           <div class="text-right">
                              <button class="btn btn-link btn-sm" id="manageTeam" aria-label="Manage Team">Manage</button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="row mobile-view">
         <!-- content for for mobile view  -->
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <!-- Sections to show/hide based on tab selection -->
                  <div class="section-content" id="section-account">
                     <!-- Account section content goes here -->
                     <div class="row">
                        <div class="panel-body scrollable-container">
                           <div class="page-header sticky-heading tw-m-0">
                              <h4><b>Accounts</b></h4>
                           </div>
                           <div id="emptyState" class="alert alert-info text-center tw-mt-5 tw-p-3" style="display:none">No accounts have been added yet.</div>
                           <div id="accountCount" class="alert alert-info text-center tw-mt-5 tw-p-3">2311 accounts by last interaction (desc.)</div>
                           <ul class="list-group" id="accountList">
                              <li class="list-group-item active">
                                 <a href="#" class="text-muted">
                                    <div class="row">
                                       <div class="col-xs-2">
                                          <img src="https://img.fullcontact.com/static/dfa0ab6849d409d61e802c732b634255_feb2b5df6e5f5443c08c36d344e559a7b643f145eaaa6c46ccd06ed5efd1f9cb" class="img-circle staff-profile-image-small" alt="Account Avatar">
                                       </div>
                                       <div class="col-xs-10">
                                          <div class="tw-flex tw-justify-between">
                                             <p class="text-left account-name">Test1</p>
                                             <p class="text-right">18 Jul</p>
                                          </div>
                                          <p class="text-left single-line-ellipsis"><span class="glyphicon glyphicon-envelope"></span> Vikash1 - Inquiry About Your WordPress Development Services</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li class="list-group-item">
                                 <a href="#" class="text-muted">
                                    <div class="row">
                                       <div class="col-xs-2">
                                          <img src="https://lib.salesflare.com/avatars/avatar_company_default_2.png" class="img-circle staff-profile-image-small" alt="Account Avatar">
                                       </div>
                                       <div class="col-xs-10">
                                          <div class="tw-flex tw-justify-between">
                                             <p class="text-left account-name">Hung Than</p>
                                             <p class="text-right">3:35 PM</p>
                                          </div>
                                          <p class="text-left single-line-ellipsis"><span class="glyphicon glyphicon-envelope"></span> Userback - [HWG - Testing] New Bug created by Anuradha Mattaparthi</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li class="list-group-item">
                                 <a href="#" class="text-muted">
                                    <div class="row">
                                       <div class="col-xs-2">
                                          <img src="https://logo.clearbit.com/nwida.org" class="img-circle staff-profile-image-small" alt="Account Avatar">
                                       </div>
                                       <div class="col-xs-10">
                                          <div class="tw-flex tw-justify-between">
                                             <p class="text-left account-name">NWIDA</p>
                                             <p class="text-right">1:53 PM</p>
                                          </div>
                                          <p class="text-left single-line-ellipsis"><span class="glyphicon glyphicon-envelope"></span> Adam Wolf - The latest news from NWIDA</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                           </ul>
                        </div>
                     </div>
                  </div>
                  <div class="section-content" id="section-timeline">
                     <!-- Timeline section content goes here -->
                     <div class="row">
                        <div class="panel-body scrollable-container">
                           <div class="page-header sticky-heading tw-m-0">
                              <h4><b><?php echo _l('email_timeline'); ?></b></h4>
                           </div>
                           <?php
                           foreach ($groupedEmails as $datetime => $emails_data) :
                              $formattedDate = (new DateTime($datetime))->format('l, F j, Y');
                           ?>
                              <div class="main-section">
                                 <div class="dateandtime text-center sticky-heading tw-my-3">
                                    <div class="custom-date-time">
                                       <?php echo $formattedDate; ?>
                                    </div>
                                 </div>
                                 <div class="panel-group" id="accordionmobile-<?php echo $datetime; ?>" role="tablist" aria-multiselectable="true">
                                    <?php foreach ($emails_data as $email) : ?>
                                       <div class="panel panel-default">
                                          <div class="panel-heading" role="tab" id="headingmobile-<?php echo $email['id']; ?>">
                                             <h4 class="panel-title">
                                                <div role="button" data-toggle="collapse" href="#collapsemobile-<?php echo $email['id']; ?>" aria-expanded="true" aria-controls="collapsemobile-<?php echo $email['id']; ?>">
                                                   <div class="tw-flex tw-justify-between">
                                                      <div class="profile">
                                                         <img src="https://localhost/pmpdev1/assets/images/user-placeholder.jpg" alt="Profile Picture" class="img img-responsive staff-profile-image-small tw-ring-1 tw-ring-offset-2 tw-ring-primary-500 tw-mx-1 tw-my-3">
                                                      </div>
                                                      <div class="middle-sec">
                                                         <div class="tw-my-3 sender-name"><?php echo $email['sender_name']; ?></div>
                                                         <div class="tw-my-2 subject single-line-ellipsis"><?php echo $email['subject']; ?></div>
                                                      </div>
                                                      <div class="icons tw-my-3 text-right">
                                                         <i class="fas fa-eye"></i>
                                                         <span class="timeline-date">04:49</span>
                                                      </div>
                                                   </div>
                                                </div>
                                             </h4>
                                          </div>
                                          <div id="collapsemobile-<?php echo $email['id']; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingmobile-<?php echo $email['id']; ?>">
                                             <div class="panel-body">
                                                <div class="tw-flex tw-justify-between">
                                                   <div class="heading-left">
                                                      <strong>From:</strong> <?php echo $email['sender']; ?><br>
                                                      <strong>To:</strong> <?php echo $email['recipient']; ?><br>
                                                   </div>
                                                   <div class="heading-right">
                                                      <span class="float-right dropdown">
                                                         <i class="fas fa-reply tw-p-2 dropdown-toggle" data-toggle="dropdown"></i> <!-- Reply icon -->
                                                         <ul class="dropdown-menu reply-dropdown">
                                                            <li><a href="#"><i class="fa fa-reply" aria-hidden="true"></i> Reply</a></li>
                                                            <li><a href="#"><i class="fa fa-reply-all" aria-hidden="true"></i> Reply All</a></li>
                                                            <li><a href="#"><i class="fa fa-share" aria-hidden="true"></i> Forward</a></li>
                                                         </ul>
                                                         <i class="fas fa-trash tw-pl-2"></i> <!-- Delete icon -->
                                                      </span>
                                                   </div>
                                                </div>
                                                <hr>
                                                <?php echo nl2br(trim($email['message'])); ?>
                                             </div>
                                          </div>
                                       </div>
                                    <?php endforeach; ?>
                                 </div>
                              </div>
                           <?php endforeach; ?>
                        </div>
                     </div>
                  </div>
                  <div class="section-content" id="section-accountinfo">
                     <!-- Account Info section content goes here -->
                     <div class="row">
                        <div class="panel-body scrollable-container">
                           <div class="row">
                              <div class="page-header sticky-heading tw-m-0">
                                 <h4><b>Account Information</b></h4>
                              </div>
                              <div class="col-xs-12">
                                 <ul class="list-unstyled">
                                    <li class="media">
                                       <div class="media-left">
                                          <i class="glyphicon glyphicon-globe"></i>
                                       </div>
                                       <div class="media-body">
                                          <a href="https://www.rohyderabad.com" target="_blank">https://www.rohyderabad.com</a>
                                       </div>
                                    </li>
                                    <li class="media">
                                       <div class="media-left">
                                          <i class="glyphicon glyphicon-info-sign"></i>
                                       </div>
                                       <div class="media-body">
                                          <p class="text-truncate">Test descriptions</p>
                                       </div>
                                    </li>
                                    <li class="media">
                                       <div class="media-left">
                                          <i class="glyphicon glyphicon-user"></i>
                                       </div>
                                       <div class="media-body">
                                          <span>12</span>
                                       </div>
                                    </li>
                                    <li class="media">
                                       <div class="media-left">
                                          <i class="glyphicon glyphicon-envelope"></i>
                                       </div>
                                       <div class="media-body">
                                          <ul class="list-unstyled">
                                             <li><a href="mailto:test@gmail.com">test@gmail.com</a></li>
                                          </ul>
                                       </div>
                                    </li>
                                    <li class="media">
                                       <div class="media-left">
                                          <i class="glyphicon glyphicon-phone"></i>
                                       </div>
                                       <div class="media-body">
                                          <ul class="list-unstyled">
                                             <li><a href="tel:09064529941">09064529941</a></li>
                                          </ul>
                                       </div>
                                    </li>
                                    <li class="media">
                                       <div class="media-left">
                                          <i class="glyphicon glyphicon-map-marker"></i>
                                       </div>
                                       <div class="media-body">
                                          <ul class="list-unstyled">
                                             <li>Ground Floor, Hatch Station, 500003 Hyderabad, Telangana, India</li>
                                          </ul>
                                       </div>
                                    </li>
                                    <li class="media">
                                       <div class="media-left">
                                          <i class="glyphicon glyphicon-cog"></i>
                                       </div>
                                       <div class="media-body">
                                          <ul class="list-unstyled">
                                             <li>Lead Status Tag: BD chat, New Lead</li>
                                             <li>Lead/Client Type: BD - Lead</li>
                                             <li>Client Level: Unknown</li>
                                             <li>Source: BD Growth Suite</li>
                                          </ul>
                                       </div>
                                    </li>
                                    <li class="media" ng-if="account.tags.length > 0">
                                       <div class="media-left">
                                          <i class="glyphicon glyphicon-tag"></i>
                                       </div>
                                       <div class="media-body">
                                          <ul class="list-inline">
                                             <li><span class="label label-default">Test</span></li>
                                          </ul>
                                       </div>
                                    </li>
                                 </ul>
                              </div>
                              <div class="col-xs-12">
                                 <div class="btn-group pull-right">
                                    <button class="btn btn-link btn-sm" type="button">Edit</button>
                                    <button class="btn btn-link btn-sm hide" type="button">Show more</button>
                                    <button class="btn btn-link btn-sm" type="button">Show less</button>
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="page-header sticky-heading">
                                 <h4><b>Contacts</b></h4>
                              </div>
                              <div id="emptyState" class="alert alert-info text-center" style="display:none">No contacts have been added yet.</div>
                              <ul class="list-group" id="contactList">
                                 <li class="list-group-item">
                                    <a href="#" class="text-muted">
                                       <div class="row">
                                          <div class="col-xs-2">
                                             <img src="https://lib.salesflare.com/avatars/avatar_tile_p_80.png" class="img-circle staff-profile-image-small" alt="Contact Avatar">
                                          </div>
                                          <div class="col-xs-8">
                                             <p class="text-left"><strong>P 1</strong></p>
                                          </div>
                                          <div class="col-xs-2 text-right">
                                             <button class="btn btn-default btn-xs" aria-label="Email P 1"><span class="glyphicon glyphicon-envelope"></span></button>
                                          </div>
                                       </div>
                                    </a>
                                 </li>
                                 <li class="list-group-item">
                                    <a href="#" class="text-muted">
                                       <div class="row">
                                          <div class="col-xs-2">
                                             <img src="https://lib.salesflare.com/avatars/avatar_tile_p_80.png" class="img-circle staff-profile-image-small" alt="Contact Avatar">
                                          </div>
                                          <div class="col-xs-8">
                                             <p class="text-left"><strong>P 2</strong></p>
                                          </div>
                                          <div class="col-xs-2 text-right">
                                             <button class="btn btn-default btn-xs" aria-label="Email P 2"><span class="glyphicon glyphicon-envelope"></span></button>
                                          </div>
                                       </div>
                                    </a>
                                 </li>
                                 <li class="list-group-item">
                                    <a href="#" class="text-muted">
                                       <div class="row">
                                          <div class="col-xs-2">
                                             <img src="https://lib.salesflare.com/avatars/avatar_tile_t_80.png" class="img-circle staff-profile-image-small" alt="Contact Avatar">
                                          </div>
                                          <div class="col-xs-8">
                                             <p class="text-left"><strong>TestBlabs</strong></p>
                                          </div>
                                          <div class="col-xs-2 text-right">
                                             <button class="btn btn-default btn-xs" aria-label="Email TestBlabs"><span class="glyphicon glyphicon-envelope"></span></button>
                                          </div>
                                       </div>
                                    </a>
                                 </li>
                                 <li class="list-group-item">
                                    <a href="#" class="text-muted">
                                       <div class="row">
                                          <div class="col-xs-2">
                                             <img src="https://lib.salesflare.com/avatars/avatar_tile_v_80.png" class="img-circle staff-profile-image-small" alt="Contact Avatar">
                                          </div>
                                          <div class="col-xs-8">
                                             <p class="text-left"><strong>Vikash</strong></p>
                                          </div>
                                          <div class="col-xs-2 text-right">
                                             <button class="btn btn-default btn-xs" aria-label="Email Vikash"><span class="glyphicon glyphicon-envelope"></span></button>
                                          </div>
                                       </div>
                                    </a>
                                 </li>
                                 <li class="list-group-item">
                                    <a href="#" class="text-muted">
                                       <div class="row">
                                          <div class="col-xs-2">
                                             <img src="https://lib.salesflare.com/avatars/avatar_tile_v_80.png" class="img-circle staff-profile-image-small" alt="Contact Avatar">
                                          </div>
                                          <div class="col-xs-8">
                                             <p class="text-left"><strong>Vikash1</strong></p>
                                          </div>
                                          <div class="col-xs-2 text-right">
                                             <button class="btn btn-default btn-xs" aria-label="Email Vikash1"><span class="glyphicon glyphicon-envelope"></span></button>
                                          </div>
                                       </div>
                                    </a>
                                 </li>
                              </ul>
                              <div class="text-right">
                                 <button class="btn btn-link btn-sm" id="manageContacts" aria-label="Manage Contacts">Manage</button>
                              </div>
                           </div>
                           <div class="row">
                              <div class="page-header sticky-heading">
                                 <h4><b>Team</b></h4>
                              </div>
                              <div id="emptyState" class="alert alert-info text-center" style="display:none">No team members have been added yet.</div>
                              <ul class="list-group" id="teamList">
                                 <li class="list-group-item">
                                    <a href="#" class="text-muted">
                                       <div class="row">
                                          <div class="col-xs-2">
                                             <img src="https://lib.salesflare.com/avatars/avatar_tile_n_80.png" class="img-circle staff-profile-image-small" alt="Team Member Avatar">
                                          </div>
                                          <div class="col-xs-8">
                                             <p class="text-left"><strong>Nasiruddin Saikh</strong></p>
                                          </div>
                                          <div class="col-xs-2 text-right">
                                             <button class="btn btn-default btn-xs" aria-label="Email Nasiruddin Saikh"><span class="glyphicon glyphicon-envelope"></span></button>
                                          </div>
                                       </div>
                                    </a>
                                 </li>
                                 <li class="list-group-item">
                                    <a href="#" class="text-muted">
                                       <div class="row">
                                          <div class="col-xs-2">
                                             <img src="https://lib.salesflare.com/avatars/avatar_tile_y_80.png" class="img-circle staff-profile-image-small" alt="Team Member Avatar">
                                          </div>
                                          <div class="col-xs-8">
                                             <p class="text-left"><strong>Yakin Shah</strong></p>
                                          </div>
                                          <div class="col-xs-2 text-right">
                                             <button class="btn btn-default btn-xs" aria-label="Email Yakin Shah"><span class="glyphicon glyphicon-envelope"></span></button>
                                          </div>
                                       </div>
                                    </a>
                                 </li>
                                 <li class="list-group-item">
                                    <a href="#" class="text-muted">
                                       <div class="row">
                                          <div class="col-xs-2">
                                             <img src="https://lib.salesflare.com/avatars/avatar_tile_t_80.png" class="img-circle staff-profile-image-small" alt="Team Member Avatar">
                                          </div>
                                          <div class="col-xs-8">
                                             <p class="text-left"><strong>TestBlabs</strong></p>
                                          </div>
                                          <div class="col-xs-2 text-right">
                                             <button class="btn btn-default btn-xs" aria-label="Email TestBlabs"><span class="glyphicon glyphicon-envelope"></span></button>
                                          </div>
                                       </div>
                                    </a>
                                 </li>
                              </ul>
                              <div class="text-right">
                                 <button class="btn btn-link btn-sm" id="manageTeam" aria-label="Manage Team">Manage</button>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- Modal box -->
      <div class="modal fade" id="modal-id" data-backdrop="static" data-keyboard="false">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <!-- <h4 class="modal-title">Modal title</h4> -->
                  <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">Compose Reply Email</h4>
               </div>
               <div class="modal-body">
                  <?php echo form_open(admin_url('email_timeline/reply_email'), ['id' => 'reply_email_form', 'class' => 'form-horizontal']); ?>
                  <?php
                  if (isset($email)) {
                    // print_r($email);
                     $from_email = $email['sender'] ?? ''; // Assuming 'sender' is the key for the sender's email
                     $to_email = $email['recipient'] ?? '';
                     $cc = $email['cc'] ?? '';
                     $bcc = $email['bcc'] ?? '';
                     $sub = $email['subject'] ?? '';
                     $body = $email['message'] ?? '';
                     $dateString = $email['created_at'] ?? '';
                     // Extract date, time, and sender email for the quoted message
                     $date = new DateTime($dateString);
                     $original_date = $date->format('M d, Y h:i:s A');
                     $original_sender = $email['sender'] ?? ''; 
                     // Construct the reply message
                     $reply_message = "<p></p><p></p>On $original_date, &lt;<a rel=\"noopener\" href=\"mailto:$original_sender\">$original_sender</a>&gt; wrote:<br> <blockquote style=\"margin:0 0 0 .8ex;border-left:1px #ccc solid;padding-left:1ex\">".htmlspecialchars_decode($body, ENT_QUOTES)."</blockquote>";
                  }
                  ?>
                  <div class="form-group">
                     <label class="control-label col-sm-2" for="from_email"><small class="req text-danger">* </small>From:</label>
                     <div class="col-sm-10">
                        <input type="email" class="form-control" id="from_email" placeholder="Enter email" name="from_email" value="<?php echo $from_email; ?>">
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="control-label col-sm-2" for="to_email"><small class="req text-danger">* </small>To:</label>
                     <div class="col-sm-10">
                        <div class="input-group">
                           <input type="email" class="form-control" id="to_email" placeholder="Enter email" name="to_email" value="<?php echo $to_email; ?>">
                           <div class="input-group-btn">
                              <button class="btn btn-default" type="button" id="btn_cc">
                                 Cc
                              </button>
                              <button class="btn btn-default" type="button" id="btn_bcc">
                                 Bcc
                              </button>
                           </div>
                        </div>
                     </div>

                  </div>
                  <div class="form-group hide" id="cc_input">
                     <label class="control-label col-sm-2" for="cc_email"><small class="req text-danger">* </small>Cc:</label>
                     <div class="col-sm-10">
                        <input type="email" class="form-control" id="cc_email" placeholder="Enter email" name="cc_email" value="<?php echo $cc; ?>">
                     </div>
                  </div>
                  <div class="form-group hide" id="bcc_input">
                     <label class="control-label col-sm-2" for="bcc_email"><small class="req text-danger">* </small>Bcc:</label>
                     <div class="col-sm-10">
                        <input type="email" class="form-control" id="bcc_email" placeholder="Enter email" name="bcc_email" value="<?php echo $bcc; ?>">
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="control-label col-sm-2" for="subject"><small class="req text-danger">* </small>Subject:</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" id="subject" placeholder="Enter subject" name="subject" value="<?php echo $sub; ?>">
                     </div>
                  </div>
                  <div class="form-group" app-field-wrapper="message">
                     <label for="message" class="control-label"><small class="req text-danger">* </small>Message</label>
                     <?php echo render_textarea('message', '', $reply_message, ['required' => 'required'], [], '', 'email-message'); ?>
                  </div>
                  <div class="form-group hide">
                     <label for="attachments">Attachments</label>
                     <input type="file" id="attachments" name="attachments[]" class="form-control" multiple>
                  </div>
               </div>
               <div class="modal-footer panel-footer text-right">
                  <button class="btn btn-primary" type="submit">Send</button>
                  <button class="btn btn-default" type="button" data-dismiss="modal">Cancel</button>
               </div>
               <?php echo form_close(); ?>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
</body>
<!-- Sidebar for account information -->
<div id="account-sidebar">
   <button id="account-sidebar-close-btn" class="btn btn-link"><i class="fa fa-times" aria-hidden="true"></i></button>
   <div id="account-info-content">
      <!-- Your account information content goes here -->
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body scrollable-container">
                  <div class="row">
                     <div class="page-header sticky-heading tw-m-0">
                        <h4><b>Account Information</b></h4>
                     </div>
                     <div class="col-xs-12">
                        <ul class="list-unstyled">
                           <li class="media">
                              <div class="media-left">
                                 <i class="glyphicon glyphicon-globe"></i>
                              </div>
                              <div class="media-body">
                                 <a href="https://www.rohyderabad.com" target="_blank">https://www.rohyderabad.com</a>
                              </div>
                           </li>
                           <li class="media">
                              <div class="media-left">
                                 <i class="glyphicon glyphicon-info-sign"></i>
                              </div>
                              <div class="media-body">
                                 <p class="text-truncate">Test descriptions</p>
                              </div>
                           </li>
                           <li class="media">
                              <div class="media-left">
                                 <i class="glyphicon glyphicon-user"></i>
                              </div>
                              <div class="media-body">
                                 <span>12</span>
                              </div>
                           </li>
                           <li class="media">
                              <div class="media-left">
                                 <i class="glyphicon glyphicon-envelope"></i>
                              </div>
                              <div class="media-body">
                                 <ul class="list-unstyled">
                                    <li><a href="mailto:test@gmail.com">test@gmail.com</a></li>
                                 </ul>
                              </div>
                           </li>
                           <li class="media">
                              <div class="media-left">
                                 <i class="glyphicon glyphicon-phone"></i>
                              </div>
                              <div class="media-body">
                                 <ul class="list-unstyled">
                                    <li><a href="tel:09064529941">09064529941</a></li>
                                 </ul>
                              </div>
                           </li>
                           <li class="media">
                              <div class="media-left">
                                 <i class="glyphicon glyphicon-map-marker"></i>
                              </div>
                              <div class="media-body">
                                 <ul class="list-unstyled">
                                    <li>Ground Floor, Hatch Station, 500003 Hyderabad, Telangana, India</li>
                                 </ul>
                              </div>
                           </li>
                           <li class="media">
                              <div class="media-left">
                                 <i class="glyphicon glyphicon-cog"></i>
                              </div>
                              <div class="media-body">
                                 <ul class="list-unstyled">
                                    <li>Lead Status Tag: BD chat, New Lead</li>
                                    <li>Lead/Client Type: BD - Lead</li>
                                    <li>Client Level: Unknown</li>
                                    <li>Source: BD Growth Suite</li>
                                 </ul>
                              </div>
                           </li>
                           <li class="media" ng-if="account.tags.length > 0">
                              <div class="media-left">
                                 <i class="glyphicon glyphicon-tag"></i>
                              </div>
                              <div class="media-body">
                                 <ul class="list-inline">
                                    <li><span class="label label-default">Test</span></li>
                                 </ul>
                              </div>
                           </li>
                        </ul>
                     </div>
                     <div class="col-xs-12">
                        <div class="btn-group pull-right">
                           <button class="btn btn-link btn-sm" type="button">Edit</button>
                           <button class="btn btn-link btn-sm hide" type="button">Show more</button>
                           <button class="btn btn-link btn-sm" type="button">Show less</button>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="page-header sticky-heading">
                        <h4><b>Contacts</b></h4>
                     </div>
                     <div id="emptyState" class="alert alert-info text-center" style="display:none">No contacts have been added yet.</div>
                     <ul class="list-group" id="contactList">
                        <li class="list-group-item">
                           <a href="#" class="text-muted">
                              <div class="row">
                                 <div class="col-xs-2">
                                    <img src="https://lib.salesflare.com/avatars/avatar_tile_p_80.png" class="img-circle staff-profile-image-small" alt="Contact Avatar">
                                 </div>
                                 <div class="col-xs-8">
                                    <p class="text-left"><strong>P 1</strong></p>
                                 </div>
                                 <div class="col-xs-2 text-right">
                                    <button class="btn btn-default btn-xs" aria-label="Email P 1"><span class="glyphicon glyphicon-envelope"></span></button>
                                 </div>
                              </div>
                           </a>
                        </li>
                        <li class="list-group-item">
                           <a href="#" class="text-muted">
                              <div class="row">
                                 <div class="col-xs-2">
                                    <img src="https://lib.salesflare.com/avatars/avatar_tile_p_80.png" class="img-circle staff-profile-image-small" alt="Contact Avatar">
                                 </div>
                                 <div class="col-xs-8">
                                    <p class="text-left"><strong>P 2</strong></p>
                                 </div>
                                 <div class="col-xs-2 text-right">
                                    <button class="btn btn-default btn-xs" aria-label="Email P 2"><span class="glyphicon glyphicon-envelope"></span></button>
                                 </div>
                              </div>
                           </a>
                        </li>
                        <li class="list-group-item">
                           <a href="#" class="text-muted">
                              <div class="row">
                                 <div class="col-xs-2">
                                    <img src="https://lib.salesflare.com/avatars/avatar_tile_t_80.png" class="img-circle staff-profile-image-small" alt="Contact Avatar">
                                 </div>
                                 <div class="col-xs-8">
                                    <p class="text-left"><strong>TestBlabs</strong></p>
                                 </div>
                                 <div class="col-xs-2 text-right">
                                    <button class="btn btn-default btn-xs" aria-label="Email TestBlabs"><span class="glyphicon glyphicon-envelope"></span></button>
                                 </div>
                              </div>
                           </a>
                        </li>
                        <li class="list-group-item">
                           <a href="#" class="text-muted">
                              <div class="row">
                                 <div class="col-xs-2">
                                    <img src="https://lib.salesflare.com/avatars/avatar_tile_v_80.png" class="img-circle staff-profile-image-small" alt="Contact Avatar">
                                 </div>
                                 <div class="col-xs-8">
                                    <p class="text-left"><strong>Vikash</strong></p>
                                 </div>
                                 <div class="col-xs-2 text-right">
                                    <button class="btn btn-default btn-xs" aria-label="Email Vikash"><span class="glyphicon glyphicon-envelope"></span></button>
                                 </div>
                              </div>
                           </a>
                        </li>
                        <li class="list-group-item">
                           <a href="#" class="text-muted">
                              <div class="row">
                                 <div class="col-xs-2">
                                    <img src="https://lib.salesflare.com/avatars/avatar_tile_v_80.png" class="img-circle staff-profile-image-small" alt="Contact Avatar">
                                 </div>
                                 <div class="col-xs-8">
                                    <p class="text-left"><strong>Vikash1</strong></p>
                                 </div>
                                 <div class="col-xs-2 text-right">
                                    <button class="btn btn-default btn-xs" aria-label="Email Vikash1"><span class="glyphicon glyphicon-envelope"></span></button>
                                 </div>
                              </div>
                           </a>
                        </li>
                     </ul>
                     <div class="text-right">
                        <button class="btn btn-link btn-sm" id="manageContacts" aria-label="Manage Contacts">Manage</button>
                     </div>
                  </div>
                  <div class="row">
                     <div class="page-header sticky-heading">
                        <h4><b>Team</b></h4>
                     </div>
                     <div id="emptyState" class="alert alert-info text-center" style="display:none">No team members have been added yet.</div>
                     <ul class="list-group" id="teamList">
                        <li class="list-group-item">
                           <a href="#" class="text-muted">
                              <div class="row">
                                 <div class="col-xs-2">
                                    <img src="https://lib.salesflare.com/avatars/avatar_tile_n_80.png" class="img-circle staff-profile-image-small" alt="Team Member Avatar">
                                 </div>
                                 <div class="col-xs-8">
                                    <p class="text-left"><strong>Nasiruddin Saikh</strong></p>
                                 </div>
                                 <div class="col-xs-2 text-right">
                                    <button class="btn btn-default btn-xs" aria-label="Email Nasiruddin Saikh"><span class="glyphicon glyphicon-envelope"></span></button>
                                 </div>
                              </div>
                           </a>
                        </li>
                        <li class="list-group-item">
                           <a href="#" class="text-muted">
                              <div class="row">
                                 <div class="col-xs-2">
                                    <img src="https://lib.salesflare.com/avatars/avatar_tile_y_80.png" class="img-circle staff-profile-image-small" alt="Team Member Avatar">
                                 </div>
                                 <div class="col-xs-8">
                                    <p class="text-left"><strong>Yakin Shah</strong></p>
                                 </div>
                                 <div class="col-xs-2 text-right">
                                    <button class="btn btn-default btn-xs" aria-label="Email Yakin Shah"><span class="glyphicon glyphicon-envelope"></span></button>
                                 </div>
                              </div>
                           </a>
                        </li>
                        <li class="list-group-item">
                           <a href="#" class="text-muted">
                              <div class="row">
                                 <div class="col-xs-2">
                                    <img src="https://lib.salesflare.com/avatars/avatar_tile_t_80.png" class="img-circle staff-profile-image-small" alt="Team Member Avatar">
                                 </div>
                                 <div class="col-xs-8">
                                    <p class="text-left"><strong>TestBlabs</strong></p>
                                 </div>
                                 <div class="col-xs-2 text-right">
                                    <button class="btn btn-default btn-xs" aria-label="Email TestBlabs"><span class="glyphicon glyphicon-envelope"></span></button>
                                 </div>
                              </div>
                           </a>
                        </li>
                     </ul>
                     <div class="text-right">
                        <button class="btn btn-link btn-sm" id="manageTeam" aria-label="Manage Team">Manage</button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
var customSettings = {
  toolbar1: 'fontselect fontsizeselect | forecolor backcolor | bold italic | custom_attachment_button | alignleft aligncenter alignright alignjustify | image link | bullist numlist | restoredraft',
  plugins: [
    'advlist autoresize autosave lists link image print hr codesample',
    'visualblocks code fullscreen',
    'media save table contextmenu',
    'paste textcolor colorpicker',
  ],
  setup: function (ed) {
    ed.on('init', function () {
      this.getDoc().body.style.fontSize = '12pt';
    });

    // Add custom attachment button with FontAwesome icon
    ed.addButton('custom_attachment_button', {
      icon: false,
      type: 'button',
      tooltip: 'Insert Attachment',
      image: '', // Leave empty as we use content for icon
      onclick: function () {
        // Create a hidden file input and trigger click
        var fileInput = $('<input type="file" style="display: none;" multiple>');
        fileInput.on('change', function (e) {
          var files = e.target.files;
          for (var i = 0; i < files.length; i++) {
            var file = files[i];
            // You can handle the file here (e.g., upload it to the server)
            // For demonstration, just inserting the file name
            ed.insertContent('&nbsp;<strong>Uploaded: ' + file.name + '</strong>&nbsp;');
          }
        });
        fileInput.click();
      },
      text: '<i class="fa fa-paperclip" aria-hidden="true"></i>' // FontAwesome icon
    });
  }
};

$(function() {
  init_editor('.email-message', customSettings);
});


   $(document).ready(function() {
      // Toggle sidebar
      $('#account-info-toggle').click(function() {
         $('#account-sidebar').toggleClass('open');
      });

      $('#account-sidebar-close-btn').click(function() {
         $('#account-sidebar').removeClass('open');
      });

      // Handle tab switching in mobile view
      $('.sticky-sub-header .tab').click(function() {
         $('.sticky-sub-header .tab').removeClass('active');
         $(this).addClass('active');

         var sectionId = '#section-' + $(this).attr('id').split('-')[1];
         console.log(sectionId);
         $('.section-content').hide();
         $(sectionId).show();
      });

      // Initially show only the Account section
      $('#section-account').show();
      $('#section-timeline').hide();
      $('#section-account-info').hide();

      $(".show-more").click(function() {
         $(".hideable").show();
         $(".show-more").hide();
         $(".show-less").show();
      });

      $(".show-less").click(function() {
         $(".hideable").hide();
         $(".show-more").show();
         $(".show-less").hide();
      });
   });
   // Toggle email recipient dropdown
   document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('btn_cc').addEventListener('click', function() {
         document.getElementById('cc_input').classList.toggle('hide');
      });

      document.getElementById('btn_bcc').addEventListener('click', function() {
         document.getElementById('bcc_input').classList.toggle('hide');
      });
   });
</script>
<script>
   $(document).ready(function() {
      if ($('body').hasClass('email_timeline')) {
         if ($(window).width() < 1024) {
            $('body').removeClass('show-sidebar').addClass('hide-sidebar');
         }
      }
   });
</script>

</html>