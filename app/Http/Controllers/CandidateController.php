<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use Auth;
use Carbon\Carbon;
use App\Candidate\Candidate;
use App\Candidate\ColdCallingStatus;
use App\Candidate\CandidateDocument;
use App\Candidate\CandidateDoc;
use App\Candidate\CandidateResume;
use App\Candidate\CandidateJoineeDocument;
use App\Candidate\CandidateJoineeDocumentDetails;
use App\Candidate\CandidateTechinicalTask;
use App\Candidate\CandidateTechinicalTaskDetail;
use App\JoineePersonal;
use App\Lead;
use App\Document;
use Illuminate\Support\Facades\File;
use Webpatser\Uuid\Uuid;
use App\Libraries\SendEmail;
use Config;
use App\EmailQueues;

class CandidateController extends Controller
{
	protected $user;

	public function __construct(Request $request){	}

	protected $validationRules = [
		'name'					=>      'required|string|min:3|max:255',
		'department_id'				=>	'required',
		'designation_id'			=>	'required',
		'doj'						=>	'required',
		'dob'						=>	'required',
		'father_name'				=>	'required|string|max:255',
		'email_id'					=>	'required|string|email|unique:candidates,email',
		'cold_call_status'			=>	'required|array',
		'commitment_agreement'		=>	'required|boolean',
		'joining_agreement'			=>	'required|boolean',
		'recruiter_name'			=>	'required|string|max:255',
		'requirement_detail'		=>	'required|string|max:255',
		'location'					=>	'required|string|max:255',
		'requirement_type'			=>	'required|numeric',
		'requirement_lead_id'		=>	'required|numeric',
		'candidate_accomodation'	=>  'required|boolean',
		'assigned_consultant_work'	=>  'required|boolean',
		'contact_number'			=>  'required|string',
	];

	protected $candidateValidationRules = [
		'techinical_lead_id'	=>  'required|numeric',	
		'buddy_coach_id'		=>  'required|numeric',
	];

	protected $candidateSysReqValidationRules = [
		'system_requirements'	=>  'required'
	];

	protected $documentRules = [
		'backUpLead'			=>	'required',
		'contract'				=>	'required',
		'joiningBonus'			=>	'required'

	];
  
  	protected $consultantTechinicalTaskValidation = [
		'clientName'		=>	'required',
		'taskAssigned'		=>	'required',
	];

	protected $trainingTechinicalTaskValidation = [
		'taskAssigned'		=>	'required'
	];

	public function moveOnboard(Request $request) {

		$id = $request->id;
		$data = DB::table("candidates")->where(["id" => $id])->update([
					"onboarding" => 1,
				]);
		return http_200(true, 'Onboarded successfully', '');
	}

	public function listCandidates(Request $request)
	{
		$sort = $request->sort;
		$order = $request->order;
		$search = $request->search;
		$limit = $request->limit;

		$candidate_list = DB::table('candidates')
			->where('onboarding', '0')
			->join('designations', 'candidates.designation_id', '=', 'designations.id')
			->join('departments', 'candidates.department_id', '=', 'departments.id')
			->join('users', 'candidates.created_by', '=', 'users.id')
			->orderBy('candidates.id', 'ASC')			
			->get(['candidates.*', 'designations.designation_name', 'departments.department', 'users.full_name']);

		if ($search) {
			$candidate_list = $candidate_list->where('name', 'LIKE', '%' . $search . '%')
				->orWhere('email', 'LIKE', '%' . $search . '%')
				->orWhere('recruiter_name', 'LIKE', '%' . $search . '%')
				->orWhere('source_of_hiring', 'LIKE', '%' . $search . '%');
			$search_list = $candidate_list->get();
			if (count($search_list) === 0) {
				$msg = 'No search results found for the query ' . $search;
				error_404(false, $msg);
				die;
			}
		}
		if ($sort && $order) {
			$list = $candidate_list->orderBy($sort, $order)->paginate($limit);
		} else {
			$list = $candidate_list;
		}

		success_200(true, $list);
	}

	public function showCandidate($id)
	{		
		$candidate = DB::table('candidates')
					->where('candidates.id', $id)
					->join('designations', 'candidates.designation_id' , '=', 'designations.id')
					->join('departments', 'candidates.department_id','=','departments.id')					
					->orderBy('candidates.id','ASC')
					->get(['candidates.*','designations.designation_name','departments.department']);						
		
		// $candidate = Candidate::find($id);
		$data = DB::table('cold_calling_status')
					->select('id','date as status_date', 'name as status')
					->where(['candidate_id' => $id])->get();
		
		// $candidate = Candidate::find($id);
		$resume_data = DB::table('candidate_resume')
		->select('id','resume_path', 'resume_name')
		->where(['candidate_id' => $id])->get();

		$candidateGuid = $candidate[0]->guid;

		$isLinkDisabled = JoineePersonal::where('guid',$candidateGuid)->first();

		$linkCloseTime = CandidateJoineeDocument::where('candidate_id',$id)->first();

		// print_r($isLinkDisabled);
		// die;
		
		if(isset($candidate[0])){
			$candidate[0]->cold_calling_status = $data; // Add Cold Call status
			$candidate[0]->resume = $resume_data; // Add resume details
			$techinical_lead_name = Lead::select('name')->find($candidate[0]->techinical_lead_id)['name'];
			$techinical_lead_name = ($techinical_lead_name != null) ? $techinical_lead_name : '';
			$candidate[0]->techinical_lead_name = $techinical_lead_name;
			$buddy_coach_name = Lead::select('name')->find($candidate[0]->buddy_coach_id)['name'];
			$buddy_coach_name = ($buddy_coach_name != null) ? $buddy_coach_name : '';
			$candidate[0]->buddy_coach_name = $buddy_coach_name;
			if($isLinkDisabled != ""){
				$candidate[0]->is_link_disabled =  $isLinkDisabled->is_link_disabled;
			}
			if($linkCloseTime != ""){
				$candidate[0]->linkCloseTime =  $linkCloseTime->close_time;
			}
		}

        if(!$candidate){
            $msg = 'Candidate with id '.$id.' cannot be found';
            error_404(false,$msg);
            die;
        }
        success_200(true,$candidate);
	}
	
	public function addCandidate(Request $request)
	{
		$userid = Auth::user()->id;
		$validator = Validator::make($request->all(), $this->validationRules);
		$uuid = Uuid::generate()->string;

		if ($validator->fails()) {
			return response()->json($validator->errors());
		}

		if ($request->file('resume')) {
			if ($request->hasFile('resume')) {
	
				$contractFile = ['resume' => $request->file('resume')];			
				$validator = Validator::make($contractFile, [
					'resume' => 'mimes:docx,doc,pdf', 
					]);
				if ($validator->fails()) {
				return response()->json($validator->errors());
				}
			}
		}

		$doj = convert_date(substr($request->doj, 0, 15));
		$dob = convert_date(substr($request->dob, 0, 15));

		$new_candidate = new Candidate();
		$new_candidate->name 			= $request->name;
		$new_candidate->department_id 	= $request->department_id;
		$new_candidate->designation_id = $request->designation_id;
		$new_candidate->date_of_birth  = $dob;
		$new_candidate->date_of_join 	= $doj;
		$new_candidate->father_name 	= $request->father_name;
		$new_candidate->email 			= $request->email_id;
		$new_candidate->skype_id 		= ($request->skype_id == "undefined") ? "" : $request->skype_id;;
		$new_candidate->cold_calling_status = '';
		$new_candidate->commitment_agreement = $request->commitment_agreement;
		$new_candidate->joining_agreement = $request->joining_agreement;
		$new_candidate->recruiter_name = $request->recruiter_name;
		$new_candidate->requirement_details = $request->requirement_detail;
		$new_candidate->location = $request->location;
		$new_candidate->created_by = $userid;
		$new_candidate->updated_by = $userid;
		$new_candidate->guid = $uuid;
		$new_candidate->requirement_lead_id = $request->requirement_lead_id;
		$new_candidate->consultant_lead_id = ($request->consultant_lead_id == "null") ? 0 : $request->consultant_lead_id;
		$new_candidate->techinical_lead_id = ($request->techinical_lead_id == "null") ? 0 : $request->techinical_lead_id;
		$new_candidate->buddy_coach_id = ($request->buddy_coach_id == "null") ? 0 : $request->buddy_coach_id;
		$new_candidate->candidate_accomodation = ($request->candidate_accomodation) ? 0 : $request->candidate_accomodation;
		$new_candidate->assigned_consultant_work = $request->assigned_consultant_work;
		$new_candidate->contact_number = $request->contact_number;
		$new_candidate->alternate_number = ($request->alternate_number == "undefined") ? "" : $request->alternate_number;;
		$new_candidate->requirement_type = $request->requirement_type;

		$candidate_saved = $new_candidate->save();

		$add_joinee_document = DB::table('joinee_document_details')->insert(['guid'=> $uuid]);

		// Get the list of cold_call_status and insert
		foreach ($request->cold_call_status as $value) {
			$data = array(
				"candidate_id" => $new_candidate->id, // get the last canditate id created to map
				"date" => convert_date(substr($value['status_date'], 0, 15)),
				"name" => $value['status'],
				"created_at" => date('Y-m-d H:i:s'),
				"updated_at" => date('Y-m-d H:i:s')
			); // create an array of data and insert into the table
			DB::table('cold_calling_status')->insert($data); // insert the newly created array in table
		}

		// Upload resume
		// $doc_upload = $request->file('resume');
		// if (!empty($doc_upload)) {
		// 	$doc_path = public_path('/uploads');
		// 	error_reporting(1);
		// 	$doc_upload = store_files($doc_path, $doc_upload);
		// 	$file_name = explode('/', $doc_upload[0]);

		// 	foreach ($doc_upload as $key) {
		// 		CandidateResume::Create([
		// 			'candidate_id'	=> $new_candidate->id,
		// 			'resume_path'	=>	$key,
		// 			'resume_name'	=>	$file_name[3],
		// 		]);
		// 	}
		// }

		if ($request->file('resume')) {				
			error_reporting(1);
			$fileDetails = addslashes(file_get_contents($request->file('resume')));
			$fileProperties = File::mimeType($request->file('resume'));
			$file_name = $request->file('resume')->getClientOriginalName();
			$doc_path = public_path('/uploads');
			$resume_details_data = array(
				'candidate_id' => $new_candidate->id,
				'resume_mime' => $fileProperties,
				'resume_data' => $fileDetails,
				'resume_name' => $file_name,	
				'resume_path' => $doc_path										
			);
			
			CandidateResume::Create($resume_details_data);
		}
		
		// For email queue 
		$get_new_candidate_details = DB::table('candidates AS C')
									->join('departments AS D','D.id','C.department_id')
									->join('designations AS DE','DE.id','C.designation_id')
									->join('users AS U','U.id','C.created_by')
									->select('C.id','C.name','C.father_name','C.date_of_join','C.guid','D.department','C.requirement_details','U.email AS recruiter','DE.designation_name')
									->where('C.id',$new_candidate->id)
									->get();
		
		$cc_recruiter_mail = $get_new_candidate_details[0]->recruiter;
											
		$to_array = array(
			'HR' => Config::get('constants.HR_HEAD'),
			'Marketing' => Config::get('constants.MARKETING_HEAD'),
			'Accounts' => Config::get('constants.ACCOUNTS_HEAD'),
			'Technical' => Config::get('constants.TECHNICAL_HEAD'),			
			'Administraion' => Config::get('constants.ADMIN_MAIL'), 
			'recruiter_mail' => $cc_recruiter_mail
		);
		
		$to = $to_array[$get_new_candidate_details[0]->department];	
		if(is_array($to)){
			if(count($to) > 1){
				$to = implode(',',$to);
			}
		}	

		$from = Config::get('constants.COMMON_FROM_EMAIL');
		$cc = array_except($to_array,$get_new_candidate_details[0]->department);
		if(array_key_exists("Technical",$cc)){
			$technical_array = implode(',',$cc['Technical']);
			$new_cc = array_except($cc,'Technical');
			$new_cc = implode(',',$new_cc);
			$cc = $technical_array.','.$new_cc;
		}else{
			$cc = implode(',',$cc);
		}
			
		$candidate_details = [];
		$candidate_details['name'] = $get_new_candidate_details[0]->name;
		$candidate_details['display_name'] = $get_new_candidate_details[0]->name;
		$candidate_details['father_name'] = $get_new_candidate_details[0]->father_name;
		$candidate_details['doj'] = $get_new_candidate_details[0]->date_of_join;
		$candidate_details['designation'] = $get_new_candidate_details[0]->designation_name;
		$candidate_details['department'] = $get_new_candidate_details[0]->department;
		$candidate_details['requirement'] = $get_new_candidate_details[0]->requirement_details;
		$candidate_details['url'] = "http://cgvakstage.com:8085/onboard/pre-on-boarding/".$get_new_candidate_details[0]->id."/edit";

		$candidate_jsonData = json_encode($candidate_details);
		// Storing data for email
		$details = array(
			'candidate_id' => $new_candidate->id,
            'from' => $from,
            'to' => $to,
            'cc' => $cc,
            'bcc' => '',
            'subject' => 'Unit Head Assigning lead to Employee',
            'message' => '',
            'template' => 'assign_lead',
            'template_details' => $candidate_jsonData,
            'attachments' => '',
            'error_message' => '',
            'priority' => 1,
        );
		EmailQueues::create($details);
		SendEmail::sendRegularEmails();

		$msg = 'Candidate details has been added successfully';
		success_200(true, $new_candidate, $msg);
	}

	public function ReSendMail(Request $request)
	{
		SendEmail::sendRegularEmails();
		
	}

	public function getFailedMail($candidate_id)
	{
		$data = EmailQueues::where('candidate_id', $candidate_id)->get();
		$msg = "Success";
		success_200(true, $data , $msg);
	}


	public function updateCandidate(Request $request)
	{
		$userid = Auth::user()->id;
		$id = $request->id;
		$candidate = Candidate::find($id);

		if(!$candidate){
			$msg = 'Candidate with id '.$id.' cannot be found';
			error_404(false,$msg);
			die;
		}
        $this->validationRules['email_id'] = 'required|string|email|unique:candidates,email,'.$id.',id';
		$validator = Validator::make($request->all(),$this->validationRules);
		
		if($validator->fails()){
			return response()->json($validator->errors());
		}

		if ($request->file('resume')) {
			if ($request->hasFile('resume')) {
	
				$contractFile = ['resume' => $request->file('resume')];			
				$validator = Validator::make($contractFile, [
					'resume' => 'mimes:docx,doc,pdf', 
					]);
				if ($validator->fails()) {
				return response()->json($validator->errors());
				}
			}
		}

		$doj = convert_date(substr($request->doj,0,15));
		$dob = convert_date(substr($request->dob,0,15));

		$update_candidate = $candidate->update([
			'name'						=>	$request->name,
			'department_id'				=>	$request->department_id,
			'designation_id'			=>	$request->designation_id,
			'date_of_birth'				=>	$dob,
			'date_of_join'				=>	$doj,
			'father_name'				=>  $request->father_name,
			'email'						=>	$request->email_id,
			'skype_id'					=>	($request->skype_id == "undefined") ? "" : $request->skype_id,
			'cold_calling_status'		=>	'',
			'commitment_agreement'		=>	$request->commitment_agreement,
			'joining_agreement'			=>	$request->joining_agreement,
			'recruiter_name'			=>	$request->recruiter_name,
			'requirement_details'		=>	$request->requirement_detail,
			'location'					=>	$request->location,
			'requirement_lead_id'		=>	$request->requirement_lead_id,
			'consultant_lead_id'		=>	($request->consultant_lead_id == "null") ? 0 : $request->consultant_lead_id,
			'techinical_lead_id'			=>	($request->techinical_lead_id == "null") ? 0 : $request->techinical_lead_id,
			'buddy_coach_id'			=>	($request->buddy_coach_id == "null") ? 0 : $request->buddy_coach_id,
			'candidate_accomodation'	=>	$request->candidate_accomodation,
			'assigned_consultant_work'	=>	$request->assigned_consultant_work,
			'contact_number'			=>	$request->contact_number,
			'alternate_number'			=>	($request->alternate_number == "undefined") ? "" : $request->alternate_number,
			'requirement_type'			=>	$request->requirement_type,
			'updated_by'				=>	$userid
		]);

		$details = DB::table('cold_calling_status')->where('candidate_id', $id)->delete();

					foreach($request->cold_call_status as $value){
						$data = array(
							"candidate_id" => $id,
							"date" => convert_date(substr($value['status_date'],0,15)),
							"name" => $value['status'],
							"created_at" => date('Y-m-d H:i:s'),
							"updated_at" => date('Y-m-d H:i:s')
						); // create an array of data and insert into the table

						DB::table('cold_calling_status')->insert($data); // insert the newly created array in table
					}
		$candidate_resume = DB::table('candidate_resume')->where('candidate_id', $id)->get();

		if ($request->file('resume')) {
						// $doc_upload = $request->file('ContractFile');
			error_reporting(1);
			$fileDetails = addslashes(file_get_contents($request->file('resume')));
			$fileProperties = File::mimeType($request->file('resume'));
			$file_name = $request->file('resume')->getClientOriginalName();
			$doc_path = public_path('/uploads');
			$resume_details_data = array(
				'candidate_id' => $id,
				'resume_mime' => $fileProperties,
				'resume_data' => $fileDetails,
				'resume_name' => $file_name,	
				'resume_path' => $doc_path						
				// 'created_by' => $request->created_by
			);
			// print_r($resume_details_data);
			// die;
			$get_previous_file = CandidateResume::where('candidate_id',$id)->get();				
				if($get_previous_file->count() == 0){
					CandidateResume::insert($resume_details_data);				
				}else{
					CandidateResume::where('candidate_id',$id)->update($resume_details_data);
				}
			
		}

		$candidate = DB::table('candidates')
					->where('candidates.id', $id)
					->join('designations', 'candidates.designation_id' , '=', 'designations.id')
					->join('departments', 'candidates.department_id','=','departments.id')
					->orderBy('candidates.id','ASC')
					->get(['candidates.*','designations.designation_name','departments.department']);
		$data = DB::table('cold_calling_status')
					->select('id','date as status_date', 'name as status')
					->where(['candidate_id' => $id])->get();
		$resume_data = DB::table('candidate_resume')
					->select('id','resume_path', 'resume_name')
					->where(['candidate_id' => $id])->get();
		if(isset($candidate[0])){
			$candidate[0]->cold_calling_status = $data; // Add Cold Call status
			$candidate[0]->resume = $resume_data; // Add resume details
			$candidate = $candidate[0];
		}

		if(!$update_candidate){
			$msg = "Candidate cannot be updated";
			bad_request(false,$msg);
			die;
		}

		// For email queue
		if($request->joinee_link_disabled == "false"){

			$updated_close_time = convert_date(substr($request->linkCloseTime, 0, 15));
			// print_r($request->joinee_link_disabled);
			// print_r($updated_close_time);
			// die;
			$update_close_Time = DB::table('candidate_joinee_documents')->where('candidate_id',$id)->update(['close_time'=>$updated_close_time]);
			$close_Time = DB::table('candidate_joinee_documents')->where('candidate_id',$id)->select('close_time')->get();

			$candidate_guid = DB::table('candidates AS C')									
							->select('C.guid')
							->where('C.id',$id)
							->first();


			// To update link diabled field
			$update_link = DB::table('joinee_personal_info')								
								->where('guid','=',$candidate_guid->guid)
								->update(['is_link_disabled' => 0]);

			$get_new_candidate_details = DB::table('candidates AS C')									
							->select('C.name','C.father_name','C.date_of_join','C.email','C.guid')
							->where('C.id',$id)
							->get();

			$from = Config::get('constants.COMMON_FROM_EMAIL');
			$to = $get_new_candidate_details[0]->email;	
			$candidate_details = [];
			$candidate_details['edit'] = true;
			$candidate_details['name'] = "$get_new_candidate_details[0]->name";
			$candidate_details['url'] = "http://cgvakstage.com:8085/joinee/".$get_new_candidate_details[0]->guid;			
			$candidate_details['doj'] = $get_new_candidate_details[0]->date_of_join;	
			$candidate_details['close_time'] = $close_Time[0]->close_time;	
			$candidate_jsonData = json_encode($candidate_details);

			$details = array(
			'candidate_id' => $id,
			'from' => $from,
			'to' => $to,
			'cc' => '',
			'bcc' => '',
			'subject' => 'Joinee Details Update',
			'message' => '',
			'template' => 'joinee_link',
			'template_details' => $candidate_jsonData,
			'attachments' => '',
			'error_message' => '',
			'priority' => 1,
			);
			EmailQueues::create($details);
			SendEmail::sendRegularEmails();
		}

		$msg = 'Candidate details updated successfully';
		success_200(true,$candidate,$msg);
	}

	public function updateCandidateDetail(Request $request)
	{
		$userid = Auth::user()->id;
		$id = $request->id;
		$candidate = Candidate::find($id);
		
		if(!$candidate){
			$msg = 'Candidate with id '.$id.' cannot be found';
			error_404(false,$msg);
			die;
		}

		$validator = Validator::make($request->all(),$this->candidateValidationRules);
		
		if($validator->fails()){
			return response()->json($validator->errors());
		}

		$update_candidate = $candidate->update([
			'techinical_lead_id'			=>	$request->techinical_lead_id,
			'buddy_coach_id'				=>	$request->buddy_coach_id,
		]);
		
		// For Email queue		
		$get_new_candidate_details = DB::table('candidates AS C')
									->join('departments AS D','D.id','C.department_id')
									->join('designations AS DE','DE.id','C.designation_id')
									->join('users AS U','U.id','C.created_by')
									->select('C.name','C.father_name','C.date_of_join','C.guid','D.department','C.requirement_details','U.email AS recruiter','DE.designation_name')
									->where('C.id',$id)
									->get();
		$cc_recruiter_mail = $get_new_candidate_details[0]->recruiter;
		
		// getting lead email									
		$get_lead_details =	DB::table('leads')->select('email_id','name')->where('id',$request->techinical_lead_id)->get();	
		$to = $get_lead_details[0]->email_id;
		
													
		$cc = Config::get('constants.HR_HEAD').','.Config::get('constants.SA_MAIL').','.$cc_recruiter_mail;	
	
		$from = Config::get('constants.COMMON_FROM_EMAIL');		

		$candidate_details = [];
		$candidate_details['lead_name'] = $get_lead_details[0]->name;
		$candidate_details['name'] = $get_new_candidate_details[0]->name;			
		$candidate_details['father_name'] = $get_new_candidate_details[0]->father_name;
		$candidate_details['doj'] = $get_new_candidate_details[0]->date_of_join;
		$candidate_details['designation'] = $get_new_candidate_details[0]->designation_name;
		$candidate_details['department'] = $get_new_candidate_details[0]->department;
		$candidate_details['requirement'] = $get_new_candidate_details[0]->requirement_details;

		$candidate_jsonData = json_encode($candidate_details);
		// Storing data for email
		$details = array(
			'candidate_id' => $id,
            'from' => $from,
            'to' => $to,
            'cc' => $cc,
            'bcc' => '',
            'subject' => 'Pleased to introduce your New Team Member!!',
            'message' => '',
            'template' => 'emp_details_to_lead',
            'template_details' => $candidate_jsonData,
            'attachments' => '',
            'error_message' => '',
            'priority' => 1,
        );
    
		EmailQueues::create($details);
		SendEmail::sendRegularEmails();

		$msg = 'Candidate details updated successfully';
		success_200(true,$msg);
	}
	
	public function updateCandidateSysReqDetail(Request $request)
	{
		$userid = Auth::user()->id;
		$id = $request->id;
		$candidate = Candidate::find($id);

		if(!$candidate){
			$msg = 'Candidate with id '.$id.' cannot be found';
			error_404(false,$msg);
			die;
		}

		$validator = Validator::make($request->all(),$this->candidateSysReqValidationRules);
		
		if($validator->fails()){
			return response()->json($validator->errors());
		}

		$update_candidate = $candidate->update([
			'system_requirements'			=>	$request->system_requirements
		]);

			// For Email queue
			$get_new_candidate_details = DB::table('candidates AS C')																		
										->select('C.name','C.date_of_join')
										->where('C.id',$id)
										->get();
			$from = Config::get('constants.COMMON_FROM_EMAIL');			
			$hr_generalist = Config::get('constants.HR_GENERALIST');				
			$sa_team = Config::get('constants.SA_MAIL');	

			$candidate_details = [];
			$candidate_details['name'] = $get_new_candidate_details[0]->name;
			$candidate_details['display_name'] = $get_new_candidate_details[0]->name;			
			$candidate_details['doj'] = $get_new_candidate_details[0]->date_of_join;			

			$candidate_jsonData = json_encode($candidate_details);
	
			$details = array(
				'candidate_id' => $id,
				'from' => $from,
				'to' => $sa_team,
				'cc' => $hr_generalist,
				'bcc' => '',
				'subject' => 'System Requirement of Employee',
				'message' => '',
				'template' => 'system_requirement_to_sa',
				'template_details' => $candidate_jsonData,
				'attachments' => '',
				'error_message' => '',
				'priority' => 1,
			);
			EmailQueues::create($details);
			SendEmail::sendRegularEmails();
			
			$msg = 'Candidate details updated successfully';
			success_200(true,$msg);
	}

	public function updateOnboarding(Request $request)
	{
		$userid = Auth::user()->id;
		$id = $request->id;
		$candidate = Candidate::find($id);

		if(!$candidate){
			$msg = 'Candidate with id '.$id.' cannot be found';
			error_404(false,$msg);
			die;
		}

		$update_candidate = $candidate->update([
			'onboarding'			=>	1,
		]);

		// For Email queue
		$get_new_candidate_details = DB::table('candidates AS C')																		
		->select('C.name','C.date_of_join')
		->where('C.id',$id)
		->get();

		$from = Config::get('constants.COMMON_FROM_EMAIL');			
		$hr_generalist = Config::get('constants.HR_GENERALIST');				
		$sa_team = Config::get('constants.SA_MAIL');	

		$candidate_details = [];
		$candidate_details['onborad'] = true;
		$candidate_details['name'] = $get_new_candidate_details[0]->name;
		$candidate_details['display_name'] = $get_new_candidate_details[0]->name;			
		$candidate_details['doj'] = $get_new_candidate_details[0]->date_of_join;			

		$candidate_jsonData = json_encode($candidate_details);

		$details = array(
		'candidate_id' => $id,
		'from' => $from,
		'to' => $sa_team,
		'cc' => $hr_generalist,
		'bcc' => '',
		'subject' => 'Onboarding',
		'message' => '',
		'template' => 'system_requirement_to_sa',
		'template_details' => $candidate_jsonData,
		'attachments' => '',
		'error_message' => '',
		'priority' => 1,
		);
		EmailQueues::create($details);
		SendEmail::sendRegularEmails();

		$msg = 'Candidate detail has been updated';
		success_200(true, '', $msg);
	}

	public function deleteCandidate($id)
	{
		$candidate = Candidate::find($id);
		if (!$candidate) {
			$msg = 'Sorry, Candidate with id ' . $id . ' cannot be found';
			error_404(false, $msg);
			die;
		}
		$deleted = $candidate->delete();
		if (!$deleted) {
			$msg = 'Cannot process the request';
			bad_request(false, $msg);
			die;
		}
		$msg = 'Candidate has been deleted';
		success_200(true, '', $msg);
	}

	public function downloadResume($id){

		$document = CandidateResume::find($id);

		$file_contents = stripslashes($document->resume_data);

		return response($file_contents)
			->header('Cache-Control', 'no-cache private')
			->header('Content-Description', 'File Transfer')
			->header('Content-Type', $document->resume_mime)
			->header('Content-length', strlen($file_contents))
			->header('Content-Disposition', 'attachment; filename="' . $document->resume_name . '"');
	}

	public function getJoineeDocDetails($document_id){
		$candidate_document = CandidateJoineeDocument::where('candidate_joinee_documents.id', $document_id)
							->leftJoin('users AS U1','candidate_joinee_documents.created_by','U1.id')
							->leftJoin('users AS U2','candidate_joinee_documents.updated_by','U2.id')
							->select('candidate_joinee_documents.*','U1.name AS created_name','U2.name AS updated_name')
							->get()->toArray();
		
		success_200(true, $candidate_document);
	}

	public function index($candidate_id, Request $request, $msg = '')
	{
		$candidate_document = CandidateJoineeDocument::where('candidate_id', $candidate_id)->get(['id'])->toArray();

		// if (empty($candidate_document)) {
		// 	$error_msg = 'Sorry, Documents details for id ' . $candidate_id . ' cannot be found';
		// 	error_404(false, $error_msg);
		// 	die;
		// }
		$data = [];
		foreach ($candidate_document as $id) {
		

		$file_data = DB::select( DB::raw( "SELECT ". $id['id'] ." AS joinee_document_id ,T4.*, T5.* FROM file_type T5 LEFT JOIN (SELECT T1.id as joinee_doc_id, type
			from candidate_joinee_documents_details as T1
			Where T1.candidate_joinee_document_id = :candidate_joinee_document_id ) T4 on T4.type = T5.id"), array(
				   'candidate_joinee_document_id' => $id['id'],
				 	));
		$data['file_details'][] = $file_data;

		}

		success_200(true, $data , $msg);
	}

	public function add($candidate_id, Request $request) // Add Candidate Documents
	{
		$validator = Validator::make($request->all(), $this->documentRules);
		if ($validator->fails()) {
			return response()->json($validator->errors());
		}
		if($request->openTime != "null"){
			$openTime = convert_date(substr($request->openTime, 0, 15));
		}
		else{
			$openTime = NULL;
		}
		
		if($request->closeTime != "null"){
			$closeTime = convert_date(substr($request->closeTime, 0, 15));
		}
		else{
			$closeTime = NULL;
		}
		$sendMail = $request->send_mail;
		// print_r($request->all());
		// print_r($closeTime);
		// die;

		DB::beginTransaction();
		try {

			$doc = CandidateJoineeDocument::Create([
				'candidate_id'					=>	 $candidate_id,
				'contract' 						=>	 $request->contract,
				'joining_commitement' 			=>	 $request->joiningCommitement,
				'salary_break_up' 	 			=>	 $request->salaryBreakUp,
				'joining_bonus' 				=>	 $request->joiningBonus,
				'back_up_lead' 					=>	 $request->backUpLead,
				'open_time'		 				=>	 $openTime,
				'close_time' 					=>	 $closeTime,
				'candidate_id'					=>	 $candidate_id,
				'contract_comment' 				=>	 $request->contractComment,
				'joining_commitement_comment' 	=>	 $request->joiningCommitementComment,
				'salary_break_up_comment' 	 	=>	 $request->salaryBreakUpComment,
				'joining_bonus_comment' 		=>	 $request->joiningBonusComment,
				'back_up_lead_comment' 			=>	 $request->backUpLeadComment,
				'created_by' 					=>	 $request->created_by,
			]);
			$doc_details_data = [];
			$doc_path = public_path('/uploads');
			$file_path = 'uploads/';
			if ($request->file('ContractFile')) {
				if ($request->hasFile('ContractFile')) {
		
					$contractFile = ['ContractFile' => $request->file('ContractFile')];			
					$validator = Validator::make($contractFile, [
						'ContractFile' => 'mimes:doc,docs,pdf', 
						]);
					if ($validator->fails()) {
					return response()->json($validator->errors());
					}
				}
				error_reporting(1);
				$fileDetails = addslashes(file_get_contents($request->file('ContractFile')));
				$fileProperties = File::mimeType($request->file('ContractFile'));
				$file_name = $request->file('ContractFile')->getClientOriginalName();
				$doc_details_data[] = array(
					'candidate_joinee_document_id' => $doc->id,
					'file_mime' => $fileProperties,
					'file_data' => $fileDetails,
					'file_name' => $file_name,
					'type' => 1,
					'path' => $file_path,
					'created_by' => $request->created_by
				);
			}

			if ($request->file('joiningCommitementFile')) {
				if ($request->hasFile('joiningCommitementFile')) {
		
					$joiningCommitementFile = ['joiningCommitementFile' => $request->file('joiningCommitementFile')];			
					$validator = Validator::make($joiningCommitementFile, [
						'joiningCommitementFile' => 'mimes:doc,docs,pdf', 
						]);
					if ($validator->fails()) {
					return response()->json($validator->errors());
					}
				}
				$doc_upload = $request->file('joiningCommitementFile');
				error_reporting(1);
				$fileDetails = addslashes(file_get_contents($request->file('joiningCommitementFile')));
				$fileProperties = File::mimeType($request->file('joiningCommitementFile'));
				$file_name = $request->file('joiningCommitementFile')->getClientOriginalName();
				$doc_details_data[] = array(
					'candidate_joinee_document_id' => $doc->id,
					'file_mime' => $fileProperties,
					'file_data' => $fileDetails,
					'file_name' => $file_name,
					'type' => 2,
					'path' => $file_path,
					'created_by' => $request->created_by
				);
			}

			if ($request->file('salaryBreakUpFile')) {
				if ($request->hasFile('salaryBreakUpFile')) {
		
					$salaryBreakUpFile = ['salaryBreakUpFile' => $request->file('salaryBreakUpFile')];			
					$validator = Validator::make($salaryBreakUpFile, [
						'salaryBreakUpFile' => 'mimes:doc,docs,pdf', 
						]);
					if ($validator->fails()) {
					return response()->json($validator->errors());
					}
				}
				$doc_upload = $request->file('salaryBreakUpFile');
				error_reporting(1);
				$fileDetails = addslashes(file_get_contents($request->file('salaryBreakUpFile')));
				$fileProperties = File::mimeType($request->file('salaryBreakUpFile'));
				$file_name = $request->file('salaryBreakUpFile')->getClientOriginalName();
				$doc_details_data[] = array(
					'candidate_joinee_document_id' => $doc->id,
					'file_mime' => $fileProperties,
					'file_data' => $fileDetails,
					'file_name' => $file_name,
					'type' => 3,
					'path' => $file_path,
					'created_by' => $request->created_by
				);
			}
			if ($doc_details_data){
				CandidateJoineeDocumentDetails::insert($doc_details_data);
			}

				$get_row_details = DB::table('candidate_joinee_documents')	
									->select('*')
									->where('candidate_id',$candidate_id)
									->get();
				$dataCount = count($get_row_details);
				// print_r($dataCount);
				// print_r($sendMail);die;

				if($dataCount == 1 && $sendMail == "true")
				{
					
				// For email queue

				$get_new_candidate_details = DB::table('candidates AS C')									
										->select('C.name','C.father_name','C.date_of_join','C.email','C.guid')
										->where('C.id',$candidate_id)
										->get();
				$close_Time = DB::table('candidate_joinee_documents')->where('candidate_id',$candidate_id)->select('close_time')->get();

				$from = Config::get('constants.COMMON_FROM_EMAIL');
				$to = $get_new_candidate_details[0]->email;	

				$candidate_details = [];
				$candidate_details['name'] = "123";
				$candidate_details['url'] = "http://cgvakstage.com:8085/joinee/".$get_new_candidate_details[0]->guid;			
				$candidate_details['doj'] = $get_new_candidate_details[0]->date_of_join;	
				$candidate_details['close_time'] = $close_Time[0]->close_time;		
				$candidate_jsonData = json_encode($candidate_details);

				$details = array(
					'candidate_id' => $candidate_id,
					'from' => $from,
					'to' => $to,
					'cc' => '',
					'bcc' => '',
					'subject' => 'Get Joinee Details',
					'message' => '',
					'template' => 'joinee_link',
					'template_details' => $candidate_jsonData,
					'attachments' => '',
					'error_message' => '',
					'priority' => 1,
				);
				EmailQueues::create($details);
				SendEmail::sendRegularEmails();
				DB::table('candidate_joinee_documents')
                ->where('candidate_id', $candidate_id)
                ->update(['is_mailed' => 1]);
			}

		} catch (\Exception $e) {
			DB::rollback();
			error_404(false, $e);
			die;
		}
		DB::commit();
		$msg = 'Documents uploaded successfully';
		$docs = $this->index($candidate_id, $request, $msg);
	}

	public function update($candidate_id, Request $request) // Update Candidate Documents
	{
		$validator = Validator::make($request->all(), $this->documentRules);
		if ($validator->fails()) {
			return response()->json($validator->errors());
		}
		// $openTime = convert_date(substr($request->openTime, 0, 15));
		// $closeTime = convert_date(substr($request->closeTime, 0, 15));
		if($request->openTime != "null"){
			$openTime = convert_date(substr($request->openTime, 0, 15));
		}
		else{
			$openTime = NULL;
		}
		
		if($request->closeTime != "null"){
			$closeTime = convert_date(substr($request->closeTime, 0, 15));
		}
		else{
			$closeTime = NULL;
		}
		$joinee_id = $request->joinee_id;

		DB::beginTransaction();
		try {
			$get_cadidate_details = CandidateJoineeDocument::find($candidate_id);

			// Checking if file exits for a candidate
		
				$update_array = [
					'contract' 						=>	 $request->contract,
					'joining_commitement' 			=>	 $request->joiningCommitement,
					'salary_break_up' 	 			=>	 $request->salaryBreakUp,
					'joining_bonus' 				=>	 $request->joiningBonus,
					'back_up_lead' 					=>	 $request->backUpLead,
					'open_time'		 				=>	 $openTime,
					'close_time' 					=>	 $closeTime,
					'contract_comment' 				=>	 $request->contractComment,
					'joining_commitement_comment' 	=>	 $request->joiningCommitementComment,
					'salary_break_up_comment' 	 	=>	 $request->salaryBreakUpComment,
					'joining_bonus_comment' 		=>	 $request->joiningBonusComment,
					'back_up_lead_comment' 			=>	 $request->backUpLeadComment,
					'updated_by' 					=>	 $request->updated_by, //have to change from front end request
				];
				$doc_path = public_path('/uploads');
				$file_path = 'uploads/';
	
					if ($request->hasFile('ContractFile')) {
						if($get_cadidate_details->contract == 0){
							$contractFile = ['ContractFile' => $request->file('ContractFile')];			
							$validator = Validator::make($contractFile, [
								'ContractFile' => 'mimes:docx,doc,pdf', 
								]);
							if ($validator->fails()) {
							return response()->json($validator->errors());
							}
						
							error_reporting(1);
							$fileDetails = addslashes(file_get_contents($request->file('ContractFile')));
							$fileProperties = File::mimeType($request->file('ContractFile'));					
							$file_name = $request->file('ContractFile')->getClientOriginalName();
							$doc_details_data = array(
								'candidate_joinee_document_id' => $candidate_id,
								'file_mime' => $fileProperties,
								'file_data' => $fileDetails,
								'file_name' => $file_name,
								'type' => 1,
								'path' => $file_path,
								'updated_by' => $request->updated_by
							);
			
							CandidateJoineeDocumentDetails::insert($doc_details_data);
						}else{
							$contractFile = ['ContractFile' => $request->file('ContractFile')];			
							$validator = Validator::make($contractFile, [
								'ContractFile' => 'mimes:docx,doc,pdf', 
								]);
							if ($validator->fails()) {
							return response()->json($validator->errors());
							}
							
							error_reporting(1);
							$fileDetails = addslashes(file_get_contents($request->file('ContractFile')));
							$fileProperties = File::mimeType($request->file('ContractFile'));
							$file_name = $request->file('ContractFile')->getClientOriginalName();
							$doc_details_data = array(
								'file_mime' => $fileProperties,
								'file_data' => $fileDetails,
								'file_name' => $file_name,
								'type' => 1,					
								'updated_by' => $request->updated_by
							);
			
							CandidateJoineeDocumentDetails::where(['candidate_joinee_document_id' => $candidate_id,'type'=>1])->update($doc_details_data);
						}
						
				}
	
				if ($request->hasFile('joiningCommitementFile')) {
					if($get_cadidate_details->joining_commitement == 0){
						$joiningCommitementFile = ['joiningCommitementFile' => $request->file('joiningCommitementFile')];			
						$validator = Validator::make($joiningCommitementFile, [
							'joiningCommitementFile' => 'mimes:docx,doc,pdf', 
							]);
						if ($validator->fails()) {
						return response()->json($validator->errors());
						}
						
						error_reporting(1);					
						$fileDetails = addslashes(file_get_contents($request->file('joiningCommitementFile')));
						$fileProperties = File::mimeType($request->file('joiningCommitementFile'));
						$file_name = $request->file('joiningCommitementFile')->getClientOriginalName();
						$doc_details_data = array(
							'candidate_joinee_document_id' => $candidate_id,
							'file_mime' => $fileProperties,
							'file_data' => $fileDetails,
							'file_name' => $file_name,
							'type' => 2,
							'path' => $file_path,
							'updated_by' => $request->updated_by
						);
		
						CandidateJoineeDocumentDetails::insert($doc_details_data);
					}else{
						$joiningCommitementFile = ['joiningCommitementFile' => $request->file('joiningCommitementFile')];			
						$validator = Validator::make($joiningCommitementFile, [
							'joiningCommitementFile' => 'mimes:docx,doc,pdf', 
							]);
						if ($validator->fails()) {
						return response()->json($validator->errors());
						}
						
						error_reporting(1);						
						$fileDetails = addslashes(file_get_contents($request->file('joiningCommitementFile')));
						$fileProperties = File::mimeType($request->file('joiningCommitementFile'));
						$file_name = $request->file('joiningCommitementFile')->getClientOriginalName();
						$doc_details_data = array(
							'file_mime' => $fileProperties,
							'file_data' => $fileDetails,
							'file_name' => $file_name,
							'type' => 2,
							// 'path' => $file_path,
							'updated_by' => $request->updated_by
						);
		
						CandidateJoineeDocumentDetails::where(['candidate_joinee_document_id' => $candidate_id,'type'=>2])->update($doc_details_data);
					}
					
				}
	
					if ($request->hasFile('salaryBreakUpFile')) {
						if($get_cadidate_details->salary_break_up == 0){
							$salaryBreakUpFile = ['salaryBreakUpFile' => $request->file('salaryBreakUpFile')];			
							$validator = Validator::make($salaryBreakUpFile, [
								'salaryBreakUpFile' => 'mimes:docx,doc,pdf', 
								]);
							if ($validator->fails()) {
							return response()->json($validator->errors());
							}
							// }
							$doc_upload = $request->file('salaryBreakUpFile');
							error_reporting(1);
							$fileDetails = addslashes(file_get_contents($request->file('salaryBreakUpFile')));
							$fileProperties = File::mimeType($request->file('salaryBreakUpFile'));
							$file_name = $request->file('salaryBreakUpFile')->getClientOriginalName();
							$doc_details_data = array(
								'candidate_joinee_document_id' => $candidate_id,
								'file_mime' => $fileProperties,
								'file_data' => $fileDetails,
								'file_name' => $file_name,
								'type' => 3,
								'path' => $file_path,
								'updated_by' => $request->updated_by
							);
							CandidateJoineeDocumentDetails::insert($doc_details_data);
						}else{
							$salaryBreakUpFile = ['salaryBreakUpFile' => $request->file('salaryBreakUpFile')];			
							$validator = Validator::make($salaryBreakUpFile, [
								'salaryBreakUpFile' => 'mimes:docx,doc,pdf', 
								]);
							if ($validator->fails()) {
							return response()->json($validator->errors());
							}
							
							error_reporting(1);						
							$fileDetails = addslashes(file_get_contents($request->file('salaryBreakUpFile')));
							$fileProperties = File::mimeType($request->file('salaryBreakUpFile'));
							$file_name = $request->file('salaryBreakUpFile')->getClientOriginalName();
							$doc_details_data = array(
								'file_mime' => $fileProperties,
								'file_data' => $fileDetails,
								'file_name' => $file_name,
								'type' => 3,
								'updated_by' => $request->updated_by
							);
							CandidateJoineeDocumentDetails::where(['candidate_joinee_document_id' => $candidate_id,'type'=>3])->update($doc_details_data);
						}
						
					}
					
					$doc = CandidateJoineeDocument::where('id','=',$candidate_id)->update($update_array);

		} 
		catch (\Exception $e) {
			DB::rollback();
			error_404(false, $e);
			die;
		}
		DB::commit();
		$msg = 'Documents uploaded successfully';
		$docs = $this->index($joinee_id, $request, $msg);
	}

	public function deleteAll($candidate_id, Request $request) //delete All Candidate Documents
	{
		// echo $candidate_id;
		// print_r($request->all());
		// die;

		$validator = Validator::make($request->all(), ['document_id' => 'required']);
		if ($validator->fails()) {
			return response()->json($validator->errors());
		}

		DB::beginTransaction();
		try {
			/* $file = CandidateDoc::where(['id'=>$request->document_id])->first();
			$path = ltrim($file->path, '/'); 
			File::delete($path);
			$file->delete(); */

			$candidate_document = CandidateJoineeDocument::where('id', $request->document_id)->get()->toArray();

			if ($candidate_document) {
				CandidateJoineeDocument::where('id', $request->document_id)->delete();		
				CandidateJoineeDocumentDetails::where(['candidate_joinee_document_id' => $request->document_id])->delete();		
			}else{
				$error_msg = 'Sorry, Documents for id ' . $request->document_id . ' cannot be found';
				error_404(false, $error_msg);
				die;
			}
			// $list = CandidateJoineeDocumentDetails::where(['candidate_joinee_document_id' => $candidate_document->id])->get()->toArray();
			
			/* $data = CandidateJoineeDocumentDetails::where(['id' => $request->document_id])->get();
			$file_contents = base64_decode($data['file_data']);
			return response($file_contents)
				->header('Cache-Control', 'no-cache private')
				->header('Content-Description', 'File Transfer')
				->header('Content-Type', $data['file_mime'])
				->header('Content-length', strlen($file_contents))
				->header('Content-Disposition', 'attachment; filename=test')
				->header('Content-Transfer-Encoding', 'binary'); */
		} catch (\Exception $e) {
			DB::rollback();
			error_404(false, $e);
		}
		DB::commit();
		$msg = 'Documents have been deleted successfully';
		success_200(true,$msg,'');
		// $this->index($candidate_id, $request, $msg);
	}


	public function deleteSingle($document_id) //delete Specific Candidate Documents
	{
		DB::beginTransaction();
		try {			

			$candidate_document = CandidateJoineeDocumentDetails::where(['id' => $document_id])->get();

			$candidate_id = $candidate_document[0]->candidate_joinee_document_id;
			$doc_type = $candidate_document[0]->type;

			if ($candidate_document) {			
				CandidateJoineeDocumentDetails::where(['id' => $document_id])->delete();		
				if($doc_type == 1){
					CandidateJoineeDocument::where('id', $candidate_id)->update(['contract' => 0]);	
					}elseif($doc_type == 2){
						CandidateJoineeDocument::where('id', $candidate_id)->update(['joining_commitement' => 0]);	
					}else{
						CandidateJoineeDocument::where('id', $candidate_id)->update(['salary_break_up' => 0]);	
					}
			}else{
				$error_msg = 'Sorry, Document for id ' . $document_id . ' cannot be found';
				error_404(false, $error_msg);
				die;
			}
		
		} catch (\Exception $e) {
			DB::rollback();
			error_404(false, $e);
		}
		DB::commit();
		$msg = 'Document File has been deleted successfully';
		success_200(true,$msg,'');		
	}

	public function download($id){

		$document = CandidateJoineeDocumentDetails::find($id);

		$file_contents = stripslashes($document->file_data);

		return response($file_contents)
			->header('Cache-Control', 'no-cache private')
			->header('Content-Description', 'File Transfer')
			->header('Content-Type', $document->mime_type)
			->header('Content-length', strlen($file_contents))
			->header('Content-Disposition', 'attachment; filename="' . $document->file_name . '"');
	}

	public function listDocuments()
	{
		$documentList = Document::get();
		$response = http_200(true, 'Success', $documentList);
		return $response;
	}

	public function addTechinicalTask($candidate_id, Request $request)// Add Techinical Task
	{
		if($request->taskId == 0){
			$validator = Validator::make($request->all(),$this->consultantTechinicalTaskValidation);
		}
		if($request->taskId == 1){
			$validator = Validator::make($request->all(),$this->trainingTechinicalTaskValidation);
		}
		if($validator->fails()){
			return response()->json($validator->errors());
		}

		DB::beginTransaction();
		try{
			$data = CandidateTechinicalTask::Create([
				'candidate_id'					=>	 $candidate_id,
				'task_id' 						=>	 $request->taskId,
				'client_name' 					=>	 $request->clientName,
				'task_assigned' 				=>	 $request->taskAssigned,
				'created_by' 					=>	 $request->createdBy,
			]);
			$id = $data->id; // get the last id created to map

				if($data->task_assigned == 1){
					foreach($request->task_details as $value){
						$data = array(
							"techinical_task_id" => $id,
							"task_detail" => (($value['taskDetails'] != 'null') ? $value['taskDetails'] :NULL),
							"task_start_date" => (($value['taskStartDate'] != 'null') ? convert_date(substr($value['taskStartDate'],0,15)) : NULL),
							"task_end_date" => (($value['taskCompletedDate'] != 'null') ? convert_date(substr($value['taskCompletedDate'],0,15)) : NULL),
							"task_status" => (($value['taskStatus'] != 'null') ? $value['taskStatus'] : NULL),
							"created_by" => $request->createdBy
						); // create an array of data and insert into the table

						DB::table('techinical_task_details')->insert($data); // insert the newly created array in table
					}
				}

				// For Email queue
			$get_new_candidate_details = DB::table('candidates AS C')	
			->join('users AS U','U.id','C.created_by')				
			->select('C.name','C.date_of_join','U.email as recruiter')
			->where('C.id',$candidate_id)
			->get();

			$from = Config::get('constants.COMMON_FROM_EMAIL');
			$to = $get_new_candidate_details[0]->recruiter;		
			$hr_generalist = Config::get('constants.HR_GENERALIST');	
			$hr_generalist=implode(",",$hr_generalist);							

			$candidate_details = [];
			$candidate_details['name'] = $get_new_candidate_details[0]->name;
			$candidate_details['display_name'] = $get_new_candidate_details[0]->name;			
			$candidate_details['doj'] = $get_new_candidate_details[0]->date_of_join;	
			$candidate_jsonData = json_encode($candidate_details);

			$details = array(
			'candidate_id' => $candidate_id,
			'from' => $from,
			'to' => $to,
			'cc' => $hr_generalist,
			'bcc' => '',
			'subject' => 'Training Task / Consultant work Details',
			'message' => '',
			'template' => 'training_consultant_details_to_recruiter',
			'template_details' => $candidate_jsonData,
			'attachments' => '',
			'error_message' => '',
			'priority' => 1,
			);
			EmailQueues::create($details);
			SendEmail::sendRegularEmails();

		}
		catch(\Exception $e){
			DB::rollback();
			error_404(false,$e);
			die;
		}
		DB::commit();
		$msg = 'Techinical Task updated successfully';
		success_200(true,$msg);
	}

	public function getTechinicalTask($id) //get the techinical task detail
	{	
		$list = array();
		$list['consultant_tech_details'] = CandidateTechinicalTask::with('candidate_techinical_taskDetail')->where(['techinical_task.task_id' => '0','techinical_task.candidate_id' => $id])->get()->toArray();
		$list['training_tech_details'] = CandidateTechinicalTask::with('candidate_techinical_taskDetail')->where(['techinical_task.task_id' => '1','techinical_task.candidate_id' => $id])->get()->toArray();
        if(!$list){
            $msg = 'Techinical Task with id '.$id.' cannot be found';
            error_404(false,$msg);
            die;
        }
        success_200(true,$list);
	}

	public function deleteTechinicalTask($candidate_id, Request $request)//delete Techinical Task Details
	{
		$validator = Validator::make($request->all(),['techinical_task_id'=> 'required']);
		if($validator->fails()){
			return response()->json($validator->errors());
		}

		try{
			$taskDetail = CandidateTechinicalTaskDetail::where(['techinical_task_id'=>$request->techinical_task_id]);
			$task = CandidateTechinicalTask::where(['id'=>$request->techinical_task_id])->first();
			if(isset($taskDetail)){
				$taskDetail->delete();
			}
			$task->delete();
			$list = array();
			$list['consultant_tech_details'] = CandidateTechinicalTask::with('candidate_techinical_taskDetail')->where(['techinical_task.task_id' => '0','techinical_task.candidate_id' => $candidate_id])->get()->toArray();
			$list['training_tech_details'] = CandidateTechinicalTask::with('candidate_techinical_taskDetail')->where(['techinical_task.task_id' => '1','techinical_task.candidate_id' => $candidate_id])->get()->toArray();
        
		}
		catch(\Exception $e){
			DB::rollback();
			error_404(false,$e);
		}
		DB::commit();
		$msg = 'Techinical task has been deleted successfully';
		success_200(true,$list,$msg);
	}

	public function updateTechinicalTask(Request $request) // Update the Techinical task details
	{
		$userid = Auth::user()->id;
		$techinicalTaskId = $request->techinicalTaskId;
		$techinicalDoc = CandidateTechinicalTask::find($techinicalTaskId);

		if(!$techinicalDoc){
			$msg = 'Joinee Details with id '.$techinicalDoc.' cannot be found';
			error_404(false,$msg);
			die;
		}
		
		if($request->taskId == 0){
			$validator = Validator::make($request->all(),$this->consultantTechinicalTaskValidation);
		}
		if($request->taskId == 1){
			$validator = Validator::make($request->all(),$this->trainingTechinicalTaskValidation);
		}
		
		if($validator->fails()){
			return response()->json($validator->errors());
		}
				$updateJoineeDocument = $techinicalDoc->update([
					'task_id' 						=>	 $request->taskId,
					'client_name' 					=>	 $request->clientName,
					'task_assigned' 				=>	 $request->taskAssigned,
					'updated_by'				    =>	 $userid
				]);

				$id = $techinicalDoc->id;
				if($request->taskAssigned == 1){

				$details = DB::table('techinical_task_details')->where('techinical_task_id', $id)->delete();

					foreach($request->task_details as $value){
						$data = array(
							"techinical_task_id" => $id,
							"task_detail" => (($value['taskDetails'] != 'null') ? $value['taskDetails'] :NULL),
							"task_start_date" => (($value['taskStartDate'] != 'null') ? convert_date(substr($value['taskStartDate'],0,15)) : NULL),
							"task_end_date" => (($value['taskCompletedDate'] != 'null') ? convert_date(substr($value['taskCompletedDate'],0,15)) : NULL),
							"task_status" => (($value['taskStatus'] != 'null') ? $value['taskStatus'] : NULL),
							"created_by" => $request->createdBy
						); // create an array of data and insert into the table


						DB::table('techinical_task_details')->insert($data); // insert the newly created array in table
					}
				}

		$msg = 'Techinical details updated successfully';
		success_200(true,$msg);
	}

	public function monthCandidates(){				
		try{
			$monthly_data = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
			$year = date('Y');
			$month_count = [];
			$total_count = [];
			for($i=1;$i<=12;$i++){
				$get_data = Candidate::whereYear('date_of_join', '=', $year)->whereMonth('date_of_join', $i)->get();
				$month_count[$i] = count($get_data);
			}

			$data = Candidate::whereYear('date_of_join', '=', $year)->select('date_of_join')->get();
			if(count($data) > 0)
			{			
				foreach($monthly_data as $key => $value){
					if ($value == date('M')) {
						$total_count[$key] = $month_count[$key+1];
						break;
					}
					else {
						$total_count[$key] = $month_count[$key+1];
					}
				}
			}
			
			$msg = 'Success';
			success_200(true,$total_count,$msg);
		}
		catch(\Exception $e){
			DB::rollback();
			error_404(false,$e);
		}
	}

	public function candidatesCount(){
		$year = date('Y');				
		try{
			// $candidate_list = DB::table('candidates')->whereYear('date_of_join', '=', $year)->count();
			$candidate_list = DB::table('candidates')->whereYear('onboarding', '=', 0)->count();
			// echo $candidate_list;die;
			$msg = 'Success';
			success_200(true,$candidate_list,$msg);
		}
		catch(\Exception $e){
			DB::rollback();
			error_404(false,$e);
		}
	}

}
