<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

defined('BASEPATH') or exit('No direct script access allowed');

use Ddeboer\Imap\Server;
use PHPMailer\PHPMailer;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Search\Flag\Unseen;
use Ddeboer\Imap\Search\Flag\Seen;
use Ddeboer\Imap\Search\Flag\Answered;
use Ddeboer\Imap\Search\Date\Since;
use Ddeboer\Imap\Search\Date\Before;
use Ddeboer\Imap\Search\Email\From;
use Ddeboer\Imap\Search\Email\To;
use Ddeboer\Imap\Search\Text\Subject;
use Ddeboer\Imap\Search\Text\Keyword;
use Ddeboer\Imap\Search\Text\Body;
use Ddeboer\Imap\Search\LogicalOperator\OrConditions;

class Email_timeline extends AdminController
{
    private $imapConfig;
    private $connection;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Email_timeline_model');
        $this->load->library('email');
        $this->load->helper('form');
        $this->load->helper('url');
    }

    public function index()
    {
        $data['title'] = _l('email_timeline');
        $data['emails'] = $this->Email_timeline_model->get_all_threads();
        $this->load->view('index', $data);
    }

    public function view($thread_id)
    {
        $data['title'] = _l('email_timeline');
        $data['emails'] = $this->Email_timeline_model->get_emails_by_thread($thread_id);
        $this->load->view('view', $data);
    }

    public function send_email_old()
    {
        $this->load->library('email');

        $recipient = $this->input->post('recipient');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');

        $this->email->from('your_email@gmail.com', 'Your Name');
        $this->email->to($recipient);
        $this->email->subject($subject);
        $this->email->message($message);

        if ($this->email->send()) {
            // Save to the database
            $this->Email_timeline_model->save_email([
                'thread_id' => 1, // Set the correct thread_id
                'recipient' => $recipient,
                'subject' => $subject,
                'message' => $message,
                'type' => 'sent',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            set_alert('success', 'Email sent successfully');
        } else {
            set_alert('danger', 'Failed to send email');
        }

        redirect(admin_url('email_timeline'));
    }
    public function send_email() {
        $this->load->view('send_email');
    }
    public function compose_email_old() {
        // SMTP configuration
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'imap.gmail.com',//'friday.mxlogin.com',  // Replace with your SMTP host
            'smtp_port' => 993, //587,                 // Replace with your SMTP port
            'smtp_user' => 'vikash.businesslabs@gmail.com', //'vik2@businesslabs.org',  // Replace with your SMTP username
            'smtp_pass' => 'gkkrzwkbqlhlmbhu',//'IVnnjwCVCCxRa',     // Replace with your SMTP password
            'mailtype'  => 'html', 
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        );

        $this->email->initialize($config);
        // Get form input
        $from_email = $this->input->post('from_email');
        $to_email = $this->input->post('to_email');
        $subject = $this->input->post('subject');
        $message = $this->input->post('emaildescription');
        $cc = $this->input->post('cc');
        $bcc = $this->input->post('bcc');

        // Email content
        $this->email->from($from_email);
        $this->email->to($to_email);
        if (!empty($cc)) {
            $this->email->cc($cc);
        }
        if (!empty($bcc)) {
            $this->email->bcc($bcc);
        }
        $this->email->subject($subject);
        $this->email->message($message);

        // Send email
        if ($this->email->send()) {
            $this->Email_timeline_model->save_email([
                'thread_id' => random_int(1000, 9999), // New email, no thread yet
                'recipient' => $to_email,
                'subject' => $subject,
                'message' => $message,
                'cc' => $cc,
                'bcc' => $bcc,
                'type' => 'sent',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $data['message'] = 'Email sent successfully.';
        } else {
            $data['message'] = $this->email->print_debugger();
        }

        // Load the view with the message
        // $this->load->view('email_form', $data);
        redirect(admin_url('email_timeline/retrieve_emails'));
    }
    public function compose_email() {
        $from_email = $this->input->post('from_email');
        $to_email = $this->input->post('to_email');
        $subject = $this->input->post('subject');
        $message = $this->input->post('emaildescription');
        $this->load->model('Emails_model');
        $this->Emails_model->send_simple_email($to_email, $subject, $message);
        redirect(admin_url('email_timeline/retrieve_emails'));
    }
    public function DdeboerSend_email($toEmail, $subject, $bodyHtml, $bodyText)
    {
        // SMTP server configuration
        $mail = new PHPMailer(true);  // Passing `true` enables exceptions

        try {
            // Server settings
            $mail->isSMTP();                                 // Set mailer to use SMTP
            $mail->Host = 'friday.mxlogin.com';              // Specify SMTP server
            $mail->SMTPAuth = true;                          // Enable SMTP authentication
            $mail->Username = 'vik2@businesslabs.org';       // SMTP username
            $mail->Password = 'IVnnjwCVCCxRa';               // SMTP password
            $mail->SMTPSecure = 'ssl';                       // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;                               // TCP port to connect to

            // Recipients
            $mail->setFrom('vik2@businesslabs.org', 'Your Name');  // Sender email and name
            $mail->addAddress($toEmail);                    // Add recipient email

            // Content
            $mail->isHTML(true);                            // Set email format to HTML
            $mail->Subject = $subject;                      // Email subject
            $mail->Body    = $bodyHtml;                     // Email body in HTML format
            $mail->AltBody = $bodyText;                     // Plain text version of the email

            // Send the email
            $mail->send();
            echo 'Message has been sent';

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    public function reply_email_view($thread_id){
        $emails = $this->Email_timeline_model->get_emails_by_thread($thread_id);
        $data = ['email' => $emails[0]];
        $this->load->view('reply_email', $data);
    }
    public function reply_email() 
    {
        
        if ($this->input->post()) {
            $original_subject = $this->input->post('original_subject');
            $original_from = $this->input->post('original_from');
            $reply_subject = 'Re: ' . $original_subject;
            $reply_message = $this->input->post('message');
            $thread_id = $this->input->post('thread_id');
            $cc = $this->input->post('cc');
            $bcc = $this->input->post('bcc');
    
            // SMTP configuration
            $config = array(
                'protocol' => 'smtp',
                'smtp_host' => 'your_smtp_host',
                'smtp_port' => 587,
                'smtp_user' => 'your_smtp_user',
                'smtp_pass' => 'your_smtp_pass',
                'mailtype'  => 'html',
                'charset'   => 'utf-8',
                'newline'   => "\r\n"
            );
            $this->email->initialize($config);
    
            $this->email->from('your_email@example.com', 'Your Name');
            $this->email->to($original_from);
            if (!empty($cc)) {
                $this->email->cc($cc);
            }
            if (!empty($bcc)) {
                $this->email->bcc($bcc);
            }
            $this->email->subject($reply_subject);
            $this->email->message($reply_message);
    
            if ($this->email->send()) {
                // Save to the database
                $this->Email_timeline_model->save_email([
                    'thread_id' => $thread_id,
                    'recipient' => $original_from,
                    'subject' => $reply_subject,
                    'message' => $reply_message,
                    'cc' => $cc,
                    'bcc' => $bcc,
                    'type' => 'reply',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                set_alert('success', 'Reply sent successfully');
            } else {
                set_alert('danger', 'Failed to send reply: ' . $this->email->print_debugger());
            }
    
            redirect(admin_url('email_timeline'));
        } else {
            $data['thread_id'] = $this->input->get('thread_id');
            $data['original_subject'] = $this->input->get('subject');
            $data['original_from'] = $this->input->get('from');
            $this->load->view('reply_email', $data);
        }
    }
    

    public function retrieve_emails_old()
    {
        // Function to decode MIME string
        function decode_mime_str($string, $charset = 'UTF-8') {
            $elements = imap_mime_header_decode($string);
            $decoded_string = '';

            foreach ($elements as $element) {
                if ($element->charset == 'default') {
                    $element->charset = 'ISO-8859-1';
                }
                $decoded_string .= iconv($element->charset, $charset, $element->text);
            }

            return $decoded_string;
        }

        // IMAP server details
        $mailbox = '{imap.gmail.com:993/imap/ssl}INBOX';
        $username = 'vikash.businesslabs@gmail.com';
        $password = 'gkkrzwkbqlhlmbhu';

        // Connect to the IMAP server
        $inbox = imap_open($mailbox, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

        // Get the current date
        $current_date = date('d-M-Y');
        $sender_email = 'vikash24.businesslabs@gmail.com';
        // $search_criteria = 'FROM "' . $sender_email . '" ON "' . $current_date . '"';
        $search_criteria = 'FROM "' . $sender_email . '"';

        // Search for unread emails from the current date
        // $emails = imap_search($inbox, 'UNSEEN SINCE "' . $current_date . '"');
        $emails = imap_search($inbox, $search_criteria);

        if ($emails) {
            // Sort emails by date in descending order
            rsort($emails);

            // Loop through each email
            foreach ($emails as $email_number) {
                // Fetch the email overview
                $overview = imap_fetch_overview($inbox, $email_number, 0);

                // Fetch the email structure
                $structure = imap_fetchstructure($inbox, $email_number);

                // Fetch the email body
                $message = imap_fetchbody($inbox, $email_number, 1.1);
                if (empty($message)) {
                    $message = imap_fetchbody($inbox, $email_number, 1);
                }

                // Decode the email subject and from fields
                $subject = decode_mime_str($overview[0]->subject);
                $from = decode_mime_str($overview[0]->from);
                $date = date('Y-m-d H:i:s', strtotime($overview[0]->date));
                $thread_id = rand(1000,9999);

                // Save email details to the database
                // $this->Email_timeline_model->save_email([
                //     'thread_id' => $thread_id,
                //     'recipient' => $username,
                //     'sender' => $from,
                //     'subject' => $subject,
                //     'message' => $message,
                //     'type' => 'received',
                //     'created_at' => $date
                // ]);

                // Print the email details (optional)
                echo "<div style='border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;'>";
                echo "<strong>Subject:</strong> " . $subject . "<br>";
                echo "<strong>From:</strong> " . $from . "<br>";
                echo "<strong>Date:</strong> " . $date . "<br>";
                echo "<strong>Message:</strong> " . $message . "<br>";
                echo "</div>";
            }
        } else {
            echo "No unread emails found.";
        }

        // Close the connection
        imap_close($inbox);
    }
    public function retrieve_emails()
    {
        // IMAP server connection parameters
        $server = new Server('imap.gmail.com', 993, 'ssl');
        $username = 'vikash.businesslabs@gmail.com';
        $password = 'gkkrzwkbqlhlmbhu';
    
        // $server = new Server('friday.mxlogin.com', 993, 'ssl');
        // $username = 'vik2@businesslabs.org';
        // $password = 'IVnnjwCVCCxRa';

        // $server = new Server('friday.mxlogin.com', 993, 'ssl');
        // $username = 'vik1@businesslabs.org';
        // $password = 'Mce4@_ex}@]~';

        // Establish IMAP connection
        $connection = $server->authenticate($username, $password);

        // Get the mailbox
        $mailbox = $connection->getMailbox('INBOX');

        // Define the current date
        $currentDate = new DateTime();
        $currentDate->setTime(0, 0); // Set to the start of the day
        $beforeDate = new DateTime();
        $beforeDate->setDate(2024, 12, 26);

        // Define search criteria
        $search = new SearchExpression();
        // $search->addCondition(new To('vikash.businesslabs@gmail.com'));
        // $search->addCondition(new From('vikash.businesslabs@gmail.com'));
        // $search->addCondition(new Before($beforeDate));
        // $search->addCondition(new Since($beforeDate));
        // $search->addCondition(new Since($currentDate));
        // $search->addCondition(new Unseen());
        // $search = new Subject('Following Up on Your Interest in Project ABC');
        //$search = new Subject('Re:');

        // $arrayOrCondition = array();
        // $arrayOrCondition[] = new Before($beforeDate);
        // $search->addCondition(new OrConditions($arrayOrCondition));


        // Fetch emails based on search criteria
        // $emails = $mailbox->getMessages($search);
        $emails = $mailbox->getMessages();

        // print_r($emails);
        // exit();
        // Check if any emails are found
        if (count($emails) == 0) {
            echo "No emails found for today.<br>";
        } else {
            // Process each email
            foreach ($emails as $email) {
                // Extract email details
                $subject = $email->getSubject();

                // Extract sender details
                $from = $email->getFrom();
                $fromAddress = $from->getAddress();
                $fromName = $from->getName();

                // Extract recipient details
                $to = $email->getTo();
                
                $toAddresses = [];
                foreach ($to as $recipient) {
                    $toAddresses[] = $recipient->getAddress();
                    $toAddresses['name'] = $recipient->getName();

                }
                // Extract reply-to details
                $replyTo = $email->getReplyTo();
                $replyToAddresses = [];
                foreach ($replyTo as $reply) {
                    $replyToAddresses[] = $reply->getAddress();
                }

                // Extract CC and BCC details
                $cc = $email->getCc();
                $bcc = $email->getBcc();

                // Extract other email details
                $emailUID = $email->getId(); // UID from the server
                $headers = $email->getHeaders();
                //print_r($headers);
                $messageID = isset($headers['message-id']) ? $headers['message-id'] : 'N/A';
                $date = $email->getDate();
                $isAnswered = $email->isAnswered();
                $isDeleted = $email->isDeleted();
                $isDraft = $email->isDraft();
                $isSeen = $email->isSeen();

                // Extract email body
                $bodyHtml = $email->getCompleteBodyHtml();
                $bodyText = $email->getCompleteBodyText();

                $body = $bodyHtml ? $bodyHtml : $bodyText;
                $isHtml = $bodyHtml ? true : false;

                // Process attachments if needed
                $attachments = [];
                if ($email->hasAttachments()) {
                    foreach ($email->getAttachments() as $attachment) {
                        $attachments[] = [
                            'filename' => $attachment->getFilename(),
                            'size' => $attachment->getSize(),
                            'content' => $attachment->getDecodedContent(),
                        ];
                    }
                }
                // $thread_id = random_int(1000, 9999);
                $thread_id = 1;
                $recipient = implode(', ', $toAddresses);
                $recipient_name = $toAddresses['name'];
                // Save email details to the database
                
                
                // Output email details
                echo "Email UID: " . $emailUID . "<br>"; // Display UID
                echo "Message-ID: " . $messageID . "<br>"; // Display Message-ID
                echo "Subject: " . $subject . "<br>";
                echo "From: " . ($fromName ? $fromName . " <" . $fromAddress . ">" : $fromAddress) . "<br>";
                echo "To: " . implode(', ', $toAddresses) . "<br>";
                echo "Reply-To: " . implode(', ', $replyToAddresses) . "<br>"; // Output Reply-To
                echo "Date: " . $date->format('Y-m-d H:i:s') . "<br>";
                echo "Answered: " . ($isAnswered ? 'Yes' : 'No') . "<br>";
                echo "Deleted: " . ($isDeleted ? 'Yes' : 'No') . "<br>";
                echo "Draft: " . ($isDraft ? 'Yes' : 'No') . "<br>";
                echo "Seen: " . ($isSeen ? 'Yes' : 'No') . "<br>";
                echo "Body: " . $body . "<br>";

                // Output attachments
                foreach ($attachments as $attachment) {
                    echo "Attachment: " . $attachment['filename'] . " (size: " . $attachment['size'] . " bytes)<br>";
                }

                echo "====================<br>";

                $body = htmlspecialchars($body, ENT_QUOTES, 'UTF-8');

                // $this->Email_timeline_model->save_email([
                //     'thread_id' => $thread_id, // New email, no thread yet
                //     'recipient' => $recipient,
                //     'recipient_name' => $recipient_name ?? '', // You may need to adjust this based on available data
                //     'sender' => $fromAddress,
                //     'sender_name' => $fromName,
                //     'cc' => $cc,
                //     'bcc' => $bcc,
                //     'subject' => $subject,
                //     'is_html' => $isHtml,
                //     'message' => $body,
                //     'email_id' => $emailID,
                //     'type' => 'received',
                //     'created_at' => $date->format('Y-m-d H:i:s')
                // ]);
            }
        }

        // Disconnect from the mailbox
        $connection->expunge();
    }
    public function retrieve_emails_tem()
    {
        
        // IMAP server connection parameters
        $server = new Server('imap.gmail.com', 993, 'ssl');
        $username = 'vikash.businesslabs@gmail.com';
        $password = 'gkkrzwkbqlhlmbhu';
        // Establish IMAP connection
        $connection = $server->authenticate($username, $password);

        // Get the mailbox
        $mailbox = $connection->getMailbox('INBOX');
         
        // Array to store unique email addresses
        $emailAddresses = [];

        // Get all messages in the mailbox
        $messages = $mailbox->getMessages();

        foreach ($messages as $message) {
            // Get sender email address
            $fromAddress = $message->getFrom()->getAddress();
            if (!in_array($fromAddress, $emailAddresses)) {
                $emailAddresses[] = $fromAddress;
                echo "Email: $fromAddress"."<br>";
            }

            // Get recipient email addresses
            // $recipients = $message->getTo();
            // foreach ($recipients as $recipient) {
            //     $recipientAddress = $recipient->getAddress();
            //     if (!in_array($recipientAddress, $emailAddresses)) {
            //         $emailAddresses[] = $recipientAddress;
            //         echo "Email: $recipientAddress\n";
            //     }
            // }

            // // Get CC recipient email addresses
            // $ccRecipients = $message->getCc();
            // foreach ($ccRecipients as $cc) {
            //     $ccAddress = $cc->getAddress();
            //     if (!in_array($ccAddress, $emailAddresses)) {
            //         $emailAddresses[] = $ccAddress;
            //     }
            // }

            // // Get BCC recipient email addresses
            // $bccRecipients = $message->getBcc();
            // foreach ($bccRecipients as $bcc) {
            //     $bccAddress = $bcc->getAddress();
            //     if (!in_array($bccAddress, $emailAddresses)) {
            //         $emailAddresses[] = $bccAddress;
            //     }
            // }
        }

        // Print all unique email addresses
        // foreach ($emailAddresses as $email) {
        //     echo "Email: $email\n";
        // }
    }
    public function retrieve_and_insert_accounts()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.salesflare.com/accounts?id=13474192',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: */*',
                'Authorization: Bearer tQPrSvrjkjqFqTSEHiLxaRrnn3naivkcZ2SLzuftd_W_K'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $dataLead = json_decode($response, true);

        // Load the model
        $this->load->model('Account_timeline_model');

        // Loop through each account and prepare data for insertion
        foreach ($dataLead as $account) {
            $data = array(
                'name' => $account['name'],
                'emails' => implode(',', array_column($account['email_addresses'], 'email')), // Convert to comma-separated
                'website' => $account['website'],
                'domain' => $account['domain'],
                'picture' => $account['picture'],
                'size' => $account['size'],
                'description' => $account['description'],
                'addresses' => implode(',', array_column($account['addresses'], 'address')), // Convert to comma-separated
                'tags' => implode(',', array_column($account['tags'], 'tag')), // Convert to comma-separated
                'phone_numbers' => implode(',', array_column($account['phone_numbers'], 'number')), // Convert to comma-separated
                'lead_type' => implode(',', array_column($account['custom']['lead/client_type'], 'name')), // Convert to comma-separated
                'lead_source' => $account['custom']['source']['name'],
                'created_at' => $account['creation_date'],
                'updated_at' => $account['modification_date'],
                'created_by' => $account['owner']['id'],
            );

            // Insert the data into the database
            $this->Account_timeline_model->new_account($data);
        }
    }
    public function retrieve_and_insert_contacts()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.salesflare.com/contacts?account=13474192',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: */*',
                'Authorization: Bearer tQPrSvrjkjqFqTSEHiLxaRrnn3naivkcZ2SLzuftd_W_K'
            ),
        ));
    
        $response = curl_exec($curl);
        curl_close($curl);
    
        $dataLead = json_decode($response, true);
    
        // Check if the response is valid
        if (!is_array($dataLead)) {
            echo "Invalid response from API";
            return;
        }
    
        // Load the model
        $this->load->model('Contact_timeline_model');
    
        // Loop through each contact and prepare data for insertion
        foreach ($dataLead as $contact) {
            $data = array(
                'name' => isset($contact['name']) ? $contact['name'] : '',
                'emails' => isset($contact['email']) ? implode(',', $contact['email']) : '',
                'domain' => isset($contact['domain']) ? $contact['domain'] : '',
                'picture' => isset($contact['picture']) ? $contact['picture'] : '',
                'addresses' => isset($contact['addresses']) ? implode(',', array_column($contact['addresses'], 'address')) : '',
                'phone_number' => isset($contact['phone_number']) ? implode(',', $contact['phone_number']) : '',
                'lead_source' => isset($contact['custom']['source']['name']) ? $contact['custom']['source']['name'] : '',
                'created_at' => isset($contact['creation_date']) ? $contact['creation_date'] : '',
                'updated_at' => isset($contact['modification_date']) ? $contact['modification_date'] : '',
                'created_by' => isset($contact['owner']['id']) ? $contact['owner']['id'] : '',
            );
    
            // Insert the data into the database
            $this->Contact_timeline_model->new_contact($data);
        }
    }
    
}
?>
