<?php

namespace App\Http\Controllers;
use DB;
use App\Libraries\SendEmail;
use Config;
use App\EmailQueues;

use Illuminate\Http\Request;
// use App\Support\Facades\DB;

class DailyCronController extends Controller
{
    public function dailyMail() {
          
        $joineeData = DB::select(DB::raw("select * from candidate_joinee_documents cjd 
        JOIN (SELECT min(id) as id, max(is_mailed) as is_mailed, candidate_id FROM candidate_joinee_documents GROUP BY candidate_id) as min_cjd 
        ON min_cjd.id = cjd.id 
        AND min_cjd.is_mailed = 0 
        WHERE cjd.open_time <= NOW()"));
        
        for($i=0; $i < count($joineeData); $i++){

                $candidate_id = $joineeData[$i]->candidate_id;

                $get_new_candidate_details = DB::table('candidates AS C')									
										->select('C.name','C.father_name','C.date_of_join','C.email','C.guid')
										->where('C.id',$candidate_id)
										->get();
				$close_Time = DB::table('candidate_joinee_documents')->where('candidate_id',$candidate_id)->select('close_time')->get();
                	
                $subject = "Get Joinee Details";
                $template = "mail.joinee_link";
                // $template = "joinee_link";
                $details = [
                    'name' => $get_new_candidate_details[0]->name,
                    'doj' => $get_new_candidate_details[0]->date_of_join,
                    'url' => "http://cgvakstage.com:8085/joinee/".$get_new_candidate_details[0]->guid,
                    'close_time' => $close_Time[0]->close_time
                ];
                $candidate_jsonData = json_encode($details);
                $from = Config::get('constants.COMMON_FROM_EMAIL');
                $mail_details = array(
					'candidate_id' => $candidate_id,
					'from' => $from,
					'to' => $get_new_candidate_details[0]->email,
					'cc' => '',
					'bcc' => '',
					'subject' => $subject,
					'message' => '',
					'template' => 'joinee_link',
					'template_details' => $candidate_jsonData,
					'attachments' => '',
					'error_message' => '',
					'priority' => 1,
				);
				EmailQueues::create($mail_details);
                \Mail::to($get_new_candidate_details[0]->email)->send(new \App\Mail\SendMail($details,$subject,$template));
                // SendEmail::sendRegularEmails();

                DB::table('candidate_joinee_documents')
                ->where('candidate_id', $candidate_id)
                ->update(['is_mailed' => 1]);

                DB::table('email_queues')
                ->where('candidate_id', $candidate_id)
                ->update(['status' => 1]);
        
        }
        dd("mails sent");
    }
}

