<?php

namespace App\Libraries;

use Mail;
use App\Mail\BirthdayWishes;
use App\Mail\DefaultTemplate;
use App\Mail\EmailError;
use App\EmailQueues;
use App\Mail\AssignLead;
use App\Mail\EmpDetailsToTechnicalLead;
use App\Mail\JoineeLink;
use App\Mail\NewJoineeDetails;
use App\Mail\PrivacyPolicy;
use App\Mail\SystemReqToSA;
use App\Mail\WelcomeJoinee;
use App\Mail\TrainingConsultantDetailsToRecruiter;
use App\Mail\NewJoineeDetailsToHR;

class SendEmail {
    public static function sendRegularEmails()
    {
        $email_details = EmailQueues::where(array('status' => 0, 'error' => 0))
            ->orderBy('priority', 'asc')
            ->take(5)
            ->get()->toarray();

        if (!empty($email_details)) {
            foreach ($email_details as $details) {
                $template = ($details['message'] != '') ? 'default' : $details['template'];
                if ($template != 'default') {
                    $details['template_details'] = json_decode($details['template_details'], true);
                }
                $update_status = EmailQueues::find($details['id']);
                $is_invalid_template = false;
                try {
                    switch ($template) {
                        case 'birthday_wishes':
                            Mail::send(new BirthdayWishes($details));
                            break;
                        case 'joinee_link':
                            Mail::send(new JoineeLink($details));
                            break;  
                        case 'assign_lead':
                            Mail::send(new AssignLead($details));
                            break;  
                        case 'emp_details_to_lead':
                            Mail::send(new EmpDetailsToTechnicalLead($details));
                            break;
                        case 'system_requirement_to_sa':
                            Mail::send(new SystemReqToSA($details));
                            break;
                        case 'training_consultant_details_to_recruiter':
                            Mail::send(new TrainingConsultantDetailsToRecruiter($details));
                            break;
                        case 'new_joinee_details':
                            Mail::send(new NewJoineeDetails($details));
                            break;
                        case 'privacy_policy':
                            Mail::send(new PrivacyPolicy($details));
                            break;
                        case 'welcome_joinee':
                            Mail::send(new WelcomeJoinee($details));
                            break;
                        case 'default':
                            Mail::send(new DefaultTemplate($details));
                            break;
                        case 'new_joinee_details_to_hr':
                            Mail::send(new NewJoineeDetailsToHR($details));
                            break;
                        default:
                            $is_invalid_template = true;
                            $update_status->error = 1;
                            $update_status->error_message = 'invalid template';
                            $update_status->save();
                            break;
                    }
                    if ($is_invalid_template) {
                        continue;
                    }
                    $update_status->status = 1;
                } catch (\Exception $e) {
                    $update_status->error = 1;
                    $update_status->error_message = $e->getMessage();
                }
                $update_status->save();
            }
        }
    }

    public static function reportFailedEmails()
    {
        $email_details = EmailQueues::where(array('status' => 0, 'error' => 1))
            ->orderBy('priority', 'asc')
            ->take(10)
            ->get()->toarray();
        if (!empty($email_details)) {
            foreach ($email_details as $details) {
                $update_status = EmailQueues::find($details['id']);
                Mail::send(new EmailError($details));
                $update_status->status = 1;
                $update_status->save();
            }
        }
    }

    public static function insertTemplateMails()
    {

        $details = array(
            'from' => 'on-boarding-test@gmail.com',
            'to' => 'testmail5210@gmail.com',
            'cc' => '',
            'bcc' => '',
            'subject' => 'Updated Template - Joinee Link Get Details',
            'message' => '',
            'template' => 'joinee_link',
            'template_details' => '{"name":"Test User","doj":"03-June-2019","url":"http://test.com"}',
            'attachments' => '',
            'error_message' => '',
            'priority' => 1,
        );
        EmailQueues::create($details);

     
        $details = array(
            'from' => 'on-boarding-test@gmail.com',
            'to' => 'testmail5210@gmail.com,testing.useonly2@gmail.com',
            'cc' => '',
            'bcc' => '',
            'subject' => 'Updated Template- Unit Head Assigning lead to employee',
            'message' => '',
            'template' => 'assign_lead',
            'template_details' => '{"name":"Test User","display_name":"Mr. Test User","father_name":"Test","doj":"03-June-2019","designation":"Sr.Software Developer","department":"Technical","requirement":"requirement"}',
            'attachments' => '',
            'error_message' => '',
            'priority' => 1,
        );
        EmailQueues::create($details);

        $details = array(
            'from' => 'on-boarding-test@gmail.com',
            'to' => 'testmail5210@gmail.com',
            'cc' => '',
            'bcc' => '',
            'subject' => 'Updated Template - Assigned Employee detail to Technical Lead',
            'message' => '',
            'template' => 'emp_details_to_lead',
            'template_details' => '{"lead_name":"Test Lead","name":"Test User","display_name":"Mr. Test User","father_name":"Test","doj":"03-June-2019","designation":"Sr.Software Developer","department":"Technical","requirement":"requirement"}',
            'attachments' => '',
            'error_message' => '',
            'priority' => 1,
        );
        EmailQueues::create($details);

        $details = array(
            'from' => 'on-boarding-test@gmail.com',
            'to' => 'testmail5210@gmail.com',
            'cc' => '',
            'bcc' => '',
            'subject' => 'Updated Template - Joinee Link Update Details',
            'message' => '',
            'template' => 'joinee_link',
            'template_details' => '{"name":"Test User","doj":"03-June-2019","url":"http://test.com","edit":true}',
            'attachments' => '',
            'error_message' => '',
            'priority' => 1,
        );
        EmailQueues::create($details);
        
        $details = array(
            'from' => 'on-boarding-test@gmail.com',
            'to' => 'testmail5210@gmail.com',
            'cc' => '',
            'bcc' => '',
            'subject' => 'Updated Template - New Joinee Details',
            'message' => '',
            'template' => 'new_joinee_details',
            'template_details' => '{"name":"Test User","father_name":"Test User2","doj":"03-June-2019","designation":"Sr.Software Developer","department":"Technical"}',
            'attachments' => '',
            'error_message' => '',
            'priority' => 1,
        );
        EmailQueues::create($details);


        $details = array(
            'from' => 'on-boarding-test@gmail.com',
            'to' => 'testmail5210@gmail.com',
            'cc' => 'testing.useonly2@gmail.com',
            'bcc' => '',
            'subject' => 'Updated Template - System Requirement to SA',
            'message' => '',
            'template' => 'system_requirement_to_sa',
            'template_details' => '{"name":"Test User","display_name":"Mr. Test User","doj":"03-June-2019"}',
            'attachments' => '',
            'error_message' => '',
            'priority' => 1,
        );
        EmailQueues::create($details);

        $details = array(
            'from' => 'on-boarding-test@gmail.com',
            'to' => 'testmail5210@gmail.com',
            'cc' => 'testing.useonly2@gmail.com',
            'bcc' => '',
            'subject' => 'Updated Template - Training Task/ Consultant work Details',
            'message' => '',
            'template' => 'training_consultant_details_to_recruiter',
            'template_details' => '{"name":"Test User","display_name":"Mr. Test User","doj":"03-June-2019"}',
            'attachments' => '',
            'error_message' => '',
            'priority' => 1,
        );
        EmailQueues::create($details);

        $details = array(
            'from' => 'on-boarding-test@gmail.com',
            'to' => 'testmail5210@gmail.com',
            'cc' => '',
            'bcc' => '',
            'subject' => 'Updated Template - Welcome Joinee',
            'message' => '',
            'template' => 'welcome_joinee',
            'template_details' => '{"name":"Test User","display_name":"Mr. Test User","doj":"03rd June 2019","designation":"Sr.Software Developer","employee_image":"avatar.jpg"}',
            'attachments' => '',
            'error_message' => '',
            'priority' => 1,
        );
        EmailQueues::create($details);

        $details = array(
            'from' => 'on-boarding-test@gmail.com',
            'to' => 'testmail5210@gmail.com',
            'cc' => '',
            'bcc' => '',
            'subject' => 'Updated Template - Privacy Policy',
            'message' => '',
            'template' => 'privacy_policy',
            'template_details' => '{}',
            'attachments' => '',
            'error_message' => '',
            'priority' => 1,
        );
        EmailQueues::create($details);

    }
}
