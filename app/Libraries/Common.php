<?php

namespace App\Libraries;
use App\User;
use App\Lead;
use App\UserDesignation;
use App\UserDetails;

class Common {

    public static function generateMailTo($mail_object,$template_details) {
        if($template_details['to'] != ''){
            $to_addresses = explode(',',$template_details['to']);
            foreach($to_addresses as $to){
              $mail_object->to($to);
            }
          }
          return $mail_object;
    }

    public static function generateMailCc($mail_object,$template_details) {
        if($template_details['cc'] != ''){
            $ccs = explode(',',$template_details['cc']);
            foreach($ccs as $cc){
              $mail_object->cc($cc);
            }
          }
          return $mail_object;
    }

    public static function generateMailBcc($mail_object,$template_details) {
        if($template_details['bcc'] != ''){
            $bccs = explode(',',$template_details['bcc']);
            foreach($bccs as $bcc){
              $mail_object->bcc($bcc);
            }
          }
          return $mail_object;
    }

    public static function generateMailAttachments($mail_object,$template_details) {
        if($template_details['attachments'] != ''){
            $attachments = explode(',',$template_details['attachments']);
            foreach($attachments as $file){
              $file_path = base_path(config('constants.IMAGE_UPLOAD_PATH').'/'.$file);
              $mail_object->attach($file_path);
            }
          }
          return $mail_object;
    }
	
	public static function getUserDetails()
	{
		$array = array(2,3,4,5,8,10,17,18,23,25,28,31,35,37,38,39,42,46,74,78,79,94,95,105,107,112,116,117,118,120,122); 
		foreach($array as $a)
		{
			$user_details = UserDetails::where([['IsActive', '=', true],['DesignationICode','=',$a]])->get();
			if(count($user_details)){
				foreach ($user_details as  $user){
					if(isset($user->EmployeeNumber) && isset($user->EmployeeCorporateEmailId)){	
						$user_designation= UserDesignation::where([['IsActive', '=', true],['DesignationICode', '=', $user->DesignationICode]])->first();
						$save = Lead::updateOrCreate(
								['emp_id' => $user->EmployeeNumber],
								['name' => $user->EmployeeDisplayName, 
								 'designation' => $user_designation->Designation,
								 'email_id' => $user->EmployeeCorporateEmailId,
								 'created_by' => '1',
								 'updated_by' => '1'
								]);
						User::updateOrCreate(
						['email' => $user->EmployeeCorporateEmailId],
						['name' => $user->LoginUserName, 
						 'full_name' => $user->EmployeeFirstName.' '.$user->EmployeeLastName,
						 'password' => bcrypt($user->LoginPassword),
						]);
					}
				}
			}
		}
		if($save === 0){
			$msg = 'User details cannot be added';
			bad_request(false,$msg);
			die;
		}
		$msg = 'User details added successfully';
		success_200(true,'',$msg);
	}
	
	public static function getUserDetailsByURL(){
			$getUserData = file_get_contents('http://172.16.30.6/appraisal/get-employees');
				$getUserData = json_decode($getUserData);			
	
				foreach ($getUserData as $user){
					$save = Lead::updateOrCreate(
						['emp_id' => $user->EmployeeNumber],
						['name' => $user->EmployeeDisplayName, 
						 'designation' => $user->Designation,
						 'email_id' => $user->EmployeeCorporateEmailId,
						 'created_by' => '1',
						 'updated_by' => '1'
						]);
					User::updateOrCreate(
					['email' => $user->EmployeeCorporateEmailId],
					['name' => $user->LoginUserName, 
					'full_name' => $user->EmployeeFirstName.' '.$user->EmployeeLastName,
					'password' => bcrypt($user->LoginPassword),
					]);
				}
				$msg = 'User details added successfully';
				success_200(true,'',$msg);
	}
}
