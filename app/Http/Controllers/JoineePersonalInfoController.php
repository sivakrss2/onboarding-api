<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Auth;
use Validator;
use DB;
use Illuminate\Support\Facades\File;
use App\Candidate\Candidate;
use App\Candidate\CandidateJoineeDocument;
use App\Joineedocument;
use App\Joineedocumentdetails;
use App\JoineeChildInfo;
use App\JoineePersonal;
use App\JoineePersonalReference;
use App\JoineeProfessionalReference;
use App\JoineePreviousCompany;
use Webpatser\Uuid\Uuid;
use App\Libraries\SendEmail;
use Config;
use App\EmailQueues;


class JoineePersonalInfoController extends Controller
{
    public function __construct(Request $request)
    {
        // if(!isset($request->token)){
        // 	return response()->json(['status'	=>	false]);
        // }

        // $this->user = JWTAuth::parseToken()->authenticate();
    }

    protected $validationRules = [
    	'first_name'		=>	'required',
    	'last_name'			=>	'required',
        'contact_number'      => 'required',
        'alternate_number'    => 'required',
        'father_name'         => 'required',
        'mother_name'         => 'required',
        'present_address'     => 'required',
        'permanent_address'   => 'required',
        'date_of_birth'       => 'required',
        'date_of_join'        => 'required',
        'email'               => 'required',
    ];

    public function index(Request $request, $msg = '')
    {
        $candidate_id = DB::table('candidates')
                        ->where('candidates.guid', $request->guid)
                        ->first(['candidates.id']);

        if($candidate_id != ""){
            $get_times = DB::table('candidate_joinee_documents')
                        ->where('candidate_joinee_documents.candidate_id', $candidate_id->id)
                        ->first();         
            $open_time = $get_times->open_time;
            $close_time = $get_times->close_time;
            $current_date = Date('Y-m-d');

            if($open_time > $current_date){
                $data = 'Url is not activated yet';
                success_200(true, $data,'success');
            }elseif($close_time < $current_date){
                $data = 'Url is expired';
                success_200(true, $data,'success');
            }else{
                $data = 'Url is not expired';
                success_200(true, $data, 'success');
            }
        } else {
            $data = 'Guid cannot be found';
            success_200(true, $data,'success');
        }

    }

    public function checkDetails(Request $request){
        
        $personalInfo = JoineePersonal::where('guid', '=',$request->guid)->first();
        $personalReference = JoineePersonalReference::where('guid', '=',$request->guid)->first();
        $previouscompany = JoineePreviousCompany::where('guid', '=',$request->guid)->first();
        $professionalReference = JoineeProfessionalReference::where('guid', '=',$request->guid)->first();
        $file_type = [1,2,3,4,5,6,7,8,9,10];
        $documentDetail = Joineedocument::where('guid', '=',$request->guid)->select('type')->get()->toArray();
        $result = "";
        if(!empty($documentDetail)){
            foreach($documentDetail as $details){
                $types[] = $details['type'];           
            }
            $type = array_unique($types);
            $result = array_diff($file_type,$type);
        }       
               
        if ($personalInfo === null || $personalReference === null || $previouscompany === null || $professionalReference === null || !empty($result)) {
            success_200(false,'No Details Found');
            // return response()->Json('No Details Found');
            // error_404(false,'No Details Found');
        }else{
            success_200(false,'All details filled');
            // return response()->Json('All details filled');
        }
    }

    public function checkDetailLinkStatus(Request $request){
        $isLinkDisabled = JoineePersonal::where('guid',$request->guid)->first();
        $is_link_disabled = 0;
        if($isLinkDisabled != ""){
            $is_link_disabled =  $isLinkDisabled->is_link_disabled;
        }

        success_200(true,$is_link_disabled,'All details filled');
        
    }

    public function joineedocdownload($id){

        $document = Joineedocument::find($id);

        $file_contents = stripslashes($document->file_data);

        return response($file_contents)
        ->header('Cache-Control', 'no-cache private')
        ->header('Content-Description', 'File Transfer')
        ->header('Content-Type', $document->mime_type)
        ->header('Content-length', strlen($file_contents))
        ->header('Content-Disposition', 'attachment; filename="' . $document->file_name . '"');
    }
    
// ALTER TABLE `joinee_personal_info` ADD `guid` VARCHAR(100) NOT NULL AFTER `joinee_id`, ADD INDEX (`guid`);
// ALTER TABLE `joinee_personal_reference` ADD `guid` VARCHAR(100) NOT NULL AFTER `joinee_id`, ADD INDEX (`guid`);
// ALTER TABLE `joinee_professional_reference` ADD `guid` VARCHAR(100) NOT NULL AFTER `joinee_id`, ADD INDEX (`guid`);
// ALTER TABLE `joinee_previous_company` ADD `guid` VARCHAR(100) NOT NULL AFTER `joinee_id`, ADD INDEX (`guid`);
public function addJoinee(Request $request)
{
    // $validator = Validator::make($request->all(), $this->validationRules);

    // if ($validator->fails()) {
    //     return response()->json($validator->errors());
    // }

    // // Save joinee personal information
    // $this->addJoineePersonalInfo($request);

    // // Save joinee personal refrence
    // $this->addJoineePersonalRefrence($request);

    // // Save joinee professional refrence
    // $this->addJoineeProfessionalRefrence($request);

    // // Save joinee previous company 
    // $this->addJoineePreviousCompany($request);

    // // Save joinee document
    // $this->addJoineeDocuments($request);


    $guid = $request->guid;
    if (!$guid) {
        $msg = 'Joinee details cannot be added';
        bad_request(false, $msg);
        die;
    }
    JoineePersonal::where('guid',$request->guid)->update(['is_link_disabled' => 1,]);

    $joinee = JoineePersonal::where('guid', $guid)->get();
    $joinee['personal_refrences'] = JoineePersonalReference::where('guid', $guid)->get();
    $joinee['professional_refrences'] = JoineeProfessionalReference::where('guid', $guid)->get();
    $joinee['previous_company'] = JoineePreviousCompany::where('guid', $guid)->get();
    // $joinee['joinee_document'] = Joineedocument::where('guid', $guid)->get();

    // print_r($joinee);
    // die;

    $candidate = DB::table('candidates')
                    ->where('candidates.guid', $guid)
                    ->join('designations', 'candidates.designation_id' , '=', 'designations.id')
                    ->join('departments', 'candidates.department_id','=','departments.id')                  
                    ->orderBy('candidates.id','ASC')
                    ->get(['candidates.*','designations.designation_name','departments.department']);

    $to = Config::get('constants.HR_HEAD');
    $from = Config::get('constants.COMMON_FROM_EMAIL');
        
    $candidate_details = [];
    $candidate_details['name'] = $candidate[0]->name;
    $candidate_details['father_name'] = $candidate[0]->father_name;
    $candidate_details['doj'] = $candidate[0]->date_of_join;
    $candidate_details['designation'] = $candidate[0]->designation_name;
    $candidate_details['department'] = $candidate[0]->department;

    $candidate_jsonData = json_encode($candidate_details);
    // Storing data for email
    $details = array(
        'from' => $from,
        'to' => $to,
        'cc' => '',
        'bcc' => '',
        'subject' => 'Candidate details',
        'message' => '',
        'template' => 'new_joinee_details_to_hr',
        'template_details' => $candidate_jsonData,
        'attachments' => '',
        'error_message' => '',
        'priority' => 1,
    );
    EmailQueues::create($details);
    SendEmail::sendRegularEmails();

    $msg = 'Joinee details added successfully';
    success_200(true, $joinee, $msg);
}

    public function addJoineePersonalInfo(Request $request)
    {
        $dob = convert_date(substr($request->date_of_birth, 0, 15));
        $spouse_dob = "";
        if($request->spouse_dob != ""){
            $spouse_dob = convert_date(substr($request->spouse_dob, 0, 15));
        }
        $addJoinee = JoineePersonal::firstOrNew(array('guid' =>  $request->guid));
        $addJoinee->first_name = $request->first_name;
        $addJoinee->last_name = $request->last_name;
        $addJoinee->contact_number = $request->contact_number;
        $addJoinee->alternate_number = $request->alternate_number;
        $addJoinee->father_name = $request->father_name;
        $addJoinee->father_contact_number = $request->father_contact_number;
        $addJoinee->mother_name = $request->mother_name;
        $addJoinee->mother_contact_number = $request->mother_contact_number;
        $addJoinee->marital_status = $request->marital_status;
        if($request->marital_status == 1){
            $addJoinee->spouse_name = $request->spouse_name;
            $addJoinee->spouse_contact_number = $request->spouse_contact_number;
            $addJoinee->spouse_dob = $spouse_dob;
        } else {
            $addJoinee->spouse_name = null;
            $addJoinee->spouse_contact_number = null;
            $addJoinee->spouse_dob = null;
        }
        if($request->marital_status == 1 && count($request->childDetails) > 0){
            $addJoinee->child = 1;
        } else {
            $addJoinee->child = 0;
        }
        $addJoinee->present_address = $request->present_address;
        $addJoinee->permanent_address = $request->permanent_address;
        $addJoinee->date_of_birth = $dob;
        $addJoinee->date_of_join = $request->date_of_join;
        $addJoinee->blood_group = $request->blood_group;
        $addJoinee->email = $request->email;
        $addJoinee->guid = $request->guid;
        $addJoinee->landmark = $request->landmark;
        $addJoinee->uan_no = $request->uan_no;
        $addJoinee->save();

        JoineeChildInfo::where('joinee_id',$addJoinee->joinee_id)->delete();

        if($request->marital_status == 1 && count($request->childDetails) > 0){
            foreach ($request->childDetails as $value) {
                $childDetails = new JoineeChildInfo();
                $childDetails->joinee_id = $addJoinee->joinee_id;
                $childDetails->child_name = $value['child_name'];
                $childDetails->child_gender = $value['child_gender'];
                $childDetails->child_dob = convert_date(substr($value['child_dob'], 0, 15));
                $childDetails->save();
            }
        }

        $joinee_personal_info = DB::table('joinee_personal_info')
                                        ->select('*')
                                        ->where(['joinee_id' =>  $addJoinee->joinee_id])->get();
                    
        $joinee_child_info = DB::table('joinee_child_info')
                                        ->select('*')
                                        ->where(['joinee_id' =>  $addJoinee->joinee_id])->get();

        if(isset($joinee_personal_info[0])){ 
            $joinee_personal_info[0]->joineeChildInfo = $joinee_child_info;
        }
        
        // return true;
        $msg = "Joinee details updated sucessfully";
        success_200(true, $joinee_personal_info, $msg);
    }

    public function addJoineePersonalRefrence(Request $request)
    { 
        JoineePersonalReference::where('guid', $request->guid)->delete();

        foreach ($request->personalInformation as $refrence) {
            $addJoineeRef = new JoineePersonalReference();
            $addJoineeRef->name = $refrence['name'];
            $addJoineeRef->designation = $refrence['designation'];
            $addJoineeRef->company_name = $refrence['company_name'];
            $addJoineeRef->phone_no = $refrence['phone_no'];
            $addJoineeRef->email = $refrence['email'];
            $addJoineeRef->relation = $refrence['relation'];
            $addJoineeRef->guid = $request->guid;
            $addJoineeRef->save();
        }
        // return true;

        $msg = "Joinee personal reference updated sucessfully";
        success_200(true, $request->personal_ref, $msg);
    }


    public function addJoineeProfessionalRefrence(Request $request)
    {
         JoineeProfessionalReference::where('guid', $request->guid)->delete();

        foreach ($request->professionalInformation as $refrence) {
            $addJoineeRef = new JoineeProfessionalReference();
            $addJoineeRef->name = $refrence['name'];
            $addJoineeRef->designation = $refrence['designation'];
            $addJoineeRef->company_name = $refrence['company_name'];
            $addJoineeRef->phone_no = $refrence['phone_no'];
            $addJoineeRef->email = $refrence['email'];
            $addJoineeRef->relation = $refrence['relation'];
            $addJoineeRef->guid = $request->guid;
            $addJoineeRef->save();
        }

        $msg = "Joinee professional reference updated sucessfully";
        success_200(true, $request->personal_ref, $msg);
    }

    public function addJoineePreviousCompany(Request $request)
    {
        JoineePreviousCompany::where('guid', $request->guid)->delete();
        
        foreach ($request->previousCompanyInformation as $refrence) {
            $addJoineeCompanyRef = new JoineePreviousCompany();
            $addJoineeCompanyRef->hr_name = $refrence['hr_name'];
            $addJoineeCompanyRef->hr_designation = $refrence['hr_designation'];
            $addJoineeCompanyRef->hr_phone_no = $refrence['hr_phone_no'];
            $addJoineeCompanyRef->hr_email = $refrence['hr_email'];

            $addJoineeCompanyRef->ra_name = $refrence['ra_name'];
            $addJoineeCompanyRef->ra_designation = $refrence['ra_designation'];
            $addJoineeCompanyRef->ra_phone_no = $refrence['ra_phone_no'];
            $addJoineeCompanyRef->ra_email = $refrence['ra_email'];

            $addJoineeCompanyRef->guid = $request->guid;
            $addJoineeCompanyRef->save();
        }

        $msg = "Joinee previous company info updated sucessfully";
        success_200(true, $request->personal_ref, $msg);
    }

    public function generateUUID()
    {
        // $uuid = Uuid::generate(1);
        $uuid = "657f3da0-a7ef-11ea-980c-6f06671364c6";

        success_200(true, $uuid->time, "success");
    }

    public function addJoineeDocuments(Request $request)
    {   
    // print_r($request -> all());
    // die;     
       foreach ($request->all() as $key => $value){
           // echo  $key.'<br>';
           // die;
           switch ($key) {
            case "photo":
                if($request->hasFile('photo')){
                    // Joineedocument::where(['guid'=>$request->guid,'type'=> 1])->delete();
                    $photoFile = ['photo' => $request->file('photo')];          
                    $validator = Validator::make($photoFile, [
                        'photo' => 'mimes:jpeg,jpg,png', 
                        ]);
                    if ($validator->fails()) {
                    return response()->json($validator->errors());
                    }

                    error_reporting(1);

                    $fileDetails = addslashes(file_get_contents($request->file('photo')));
                    $fileProperties = File::mimeType($request->file('photo'));
                    $file_name = $request->file('photo')->getClientOriginalName();
                    // $addJoineeDocument= new Joineedocument();
                    $addJoineeDocument= Joineedocument::firstOrNew(array('type' => 1,'guid' =>  $request->guid));
                    $addJoineeDocument->guid = $request->guid;
                    $addJoineeDocument->file_mime = $fileProperties;
                    $addJoineeDocument->file_data = $fileDetails;
                    $addJoineeDocument->file_name = $file_name;
                    $addJoineeDocument->type = 1;
                    $addJoineeDocument->save();         
                    Joineedocumentdetails::where('guid',$request->guid)->update(['photo' => 1,'guid'=> $request->guid]);
                }
            break;
            case "aadhar_card":
                if($request->hasFile('aadhar_card')){
                    // Joineedocument::where(['guid'=>$request->guid,'type'=> 2])->delete();
                    $aadharCardFile = ['aadhar_card' => $request->file('aadhar_card')];         
                        $validator = Validator::make($aadharCardFile, [
                                'aadhar_card' => 'mimes:jpeg,jpg,png,doc,docx,pdf', 
                            ]);
                        if ($validator->fails()) {
                        return response()->json($validator->errors());
                        }
        
                    error_reporting(1);
        
                    $fileDetails = addslashes(file_get_contents($request->file('aadhar_card')));
                    $fileProperties = File::mimeType($request->file('aadhar_card'));
                    $file_name = $request->file('aadhar_card')->getClientOriginalName();    
                    // $addJoineeDocument= new Joineedocument(); 
                    $addJoineeDocument= Joineedocument::firstOrNew(array('type' => 2,'guid' =>  $request->guid));  
                    $addJoineeDocument->guid = $request->guid;
                    $addJoineeDocument->file_mime = $fileProperties;
                    $addJoineeDocument->file_data = $fileDetails;
                    $addJoineeDocument->file_name = $file_name;
                    $addJoineeDocument->type = 2;
                    $addJoineeDocument->save();         
                    Joineedocumentdetails::where('guid',$request->guid)->update(['aadhar_card' => 1,'guid'=> $request->guid]);
                }
            break;
            case "pan_card":
                if($request->hasFile('pan_card')){
                    // Joineedocument::where(['guid'=>$request->guid,'type'=> 3])->delete();
                    $panCardFile = ['pan_card' => $request->file('pan_card')];          
                        $validator = Validator::make($panCardFile, [
                            'pan_card' => 'mimes:jpeg,jpg,png,doc,docx,pdf', 
                            ]);
                        if ($validator->fails()) {
                        return response()->json($validator->errors());
                        }
        
                    error_reporting(1);
        
                    $fileDetails = addslashes(file_get_contents($request->file('pan_card')));
                    $fileProperties = File::mimeType($request->file('pan_card'));
                    $file_name = $request->file('pan_card')->getClientOriginalName();
                    // $addJoineeDocument= new Joineedocument();
                    $addJoineeDocument= Joineedocument::firstOrNew(array('type' => 3,'guid' =>  $request->guid));   
                    $addJoineeDocument->guid = $request->guid;                  
                    $addJoineeDocument->file_mime = $fileProperties;
                    $addJoineeDocument->file_data = $fileDetails;
                    $addJoineeDocument->file_name = $file_name;
                    $addJoineeDocument->type = 3;
                    $addJoineeDocument->check_box = 0;
                    $addJoineeDocument->reason = null;
                    $addJoineeDocument->save();         
                    Joineedocumentdetails::where('guid',$request->guid)->update(['pan_card' => 1,'guid'=> $request->guid]);
                }else{
                    if($request->pancard_check_box == true){
                        $reason = 'No Reason Specified';
                        if(!empty($request->pancard_text_area)){
                            $reason = $request->pancard_text_area;
                        }
                        // $addJoineeDocument= new Joineedocument();
                         $addJoineeDocument= Joineedocument::firstOrNew(array('type' => 3,'guid' =>  $request->guid));   
                        $addJoineeDocument->guid = $request->guid;
                        $addJoineeDocument->check_box = 1;
                        $addJoineeDocument->reason = $reason;                       
                        $addJoineeDocument->type = 3;
                        $addJoineeDocument->save();
                        Joineedocumentdetails::where('guid',$request->guid)->update(['pan_card' => 0,'guid'=> $request->guid]);     
                    }
                }
            break;
            case "passport":
                if($request->hasFile('passport')){
                    // Joineedocument::where(['guid'=>$request->guid,'type'=> 4])->delete();
                    $passportFile = ['passport' => $request->file('passport')];         
                        $validator = Validator::make($passportFile, [
                            'passport' => 'mimes:jpeg,jpg,png,doc,docx,pdf', 
                            ]);
                        if ($validator->fails()) {
                        return response()->json($validator->errors());
                        }
        
                    error_reporting(1);
        
                    $fileDetails = addslashes(file_get_contents($request->file('passport')));
                    $fileProperties = File::mimeType($request->file('passport'));
                    $file_name = $request->file('passport')->getClientOriginalName();   
                    // $addJoineeDocument= new Joineedocument(); 
                    $addJoineeDocument= Joineedocument::firstOrNew(array('type' => 4,'guid' =>  $request->guid));  
                    $addJoineeDocument->guid = $request->guid;
                    $addJoineeDocument->file_mime = $fileProperties;
                    $addJoineeDocument->file_data = $fileDetails;
                    $addJoineeDocument->file_name = $file_name;
                    $addJoineeDocument->type = 4;
                    $addJoineeDocument->check_box = 0;
                    $addJoineeDocument->reason = null;
                    $addJoineeDocument->save();         
                    Joineedocumentdetails::where('guid',$request->guid)->update(['passport' => 1,'guid'=> $request->guid]);
                }else{
                    if($request->passport_check_box == true){
                        $reason = 'No Reason Specified';
                        if(!empty($request->passport_text_area)){
                            $reason = $request->passport_text_area;
                        }
                        // $addJoineeDocument= new Joineedocument();
                        $addJoineeDocument= Joineedocument::firstOrNew(array('type' => 4,'guid' =>  $request->guid));
                        $addJoineeDocument->guid = $request->guid;
                        $addJoineeDocument->check_box = 1;
                        $addJoineeDocument->reason = $reason;                       
                        $addJoineeDocument->type = 4;
                        $addJoineeDocument->save();
                        Joineedocumentdetails::where('guid',$request->guid)->update(['passport' => 0, 'guid'=> $request->guid]);        
                    }
                }
            break;
            case "form_16":
                if($request->hasFile('form_16')){
                    // Joineedocument::where(['guid'=>$request->guid,'type'=> 8])->delete();
                    $form16File = ['form_16' => $request->file('form_16')];         
                        $validator = Validator::make($form16File, [
                            'form_16' => 'mimes:jpeg,jpg,png,doc,docx,pdf', 
                            ]);
                        if ($validator->fails()) {
                        return response()->json($validator->errors());
                        }
        
                    error_reporting(1);
        
                    $fileDetails = addslashes(file_get_contents($request->file('form_16')));
                    $fileProperties = File::mimeType($request->file('form_16'));
                    $file_name = $request->file('form_16')->getClientOriginalName();    
                    // $addJoineeDocument= new Joineedocument(); 
                    $addJoineeDocument= Joineedocument::firstOrNew(array('type' => 8,'guid' =>  $request->guid));  
                    $addJoineeDocument->guid = $request->guid;
                    $addJoineeDocument->file_mime = $fileProperties;
                    $addJoineeDocument->file_data = $fileDetails;
                    $addJoineeDocument->file_name = $file_name;
                    $addJoineeDocument->type = 8;
                    $addJoineeDocument->check_box = 0;
                    $addJoineeDocument->reason = null;
                    $addJoineeDocument->save();         
                    Joineedocumentdetails::where('guid',$request->guid)->update(['form_16' => 1,'guid'=> $request->guid]);
                }else{
                    if($request->form16_check_box == true){
                        $reason = 'No Reason Specified';
                        if(!empty($request->form16_text_area)){
                            $reason = $request->form16_text_area;
                        }
                        // $addJoineeDocument= new Joineedocument();
                        $addJoineeDocument= Joineedocument::firstOrNew(array('type' => 8,'guid' =>  $request->guid));    
                        $addJoineeDocument->guid = $request->guid;
                        $addJoineeDocument->check_box = 1;
                        $addJoineeDocument->reason = $reason;                       
                        $addJoineeDocument->type = 8;
                        $addJoineeDocument->save();
                        Joineedocumentdetails::where('guid',$request->guid)->update(['form_16' => 0,'guid'=> $request->guid]);      
                    }
                }
            break;
            case "pay_slips":
                // if($request->hasFile('pay_slips')){
                if($request->pay_slips != 'null'){
                    Joineedocument::where(['guid'=>$request->guid,'type'=> 9, 'check_box' => 1])->delete();
                    foreach($request->pay_slips as $letter){
                        $paySlipFile = ['pay_slips' => $letter];            
                        $validator = Validator::make($paySlipFile, [
                            'pay_slips' => 'mimes:jpeg,jpg,png,doc,docx,pdf', 
                            ]);
                        if ($validator->fails()) {
                        return response()->json($validator->errors());
                        }
                        
                        error_reporting(1);
        
                        $fileDetails = addslashes(file_get_contents($letter));
                        $fileProperties = File::mimeType($letter);
                        $file_name = $letter->getClientOriginalName();  
                        $addJoineeDocument= new Joineedocument();   
                        $addJoineeDocument->guid = $request->guid;
                        $addJoineeDocument->file_mime = $fileProperties;
                        $addJoineeDocument->file_data = $fileDetails;
                        $addJoineeDocument->file_name = $file_name;
                        $addJoineeDocument->type = 9;
                        $addJoineeDocument->save();         
                        Joineedocumentdetails::where('guid',$request->guid)->update(['pay_slips' => 1,'guid'=> $request->guid]);
                    }
                }else{
                    if($request->payslip_check_box == true){
                        $reason = 'No Reason Specified';
                        if(!empty($request->payslip_text_area)){
                            $reason = $request->payslip_text_area;
                        }
                        $addJoineeDocument= Joineedocument::firstOrNew(array('type' => 9,'guid' =>  $request->guid));    
                        // $addJoineeDocument= new Joineedocument();   
                        $addJoineeDocument->guid = $request->guid;
                        $addJoineeDocument->check_box = 1;
                        $addJoineeDocument->reason = $reason;                       
                        $addJoineeDocument->type = 9;
                        $addJoineeDocument->save();
                        Joineedocumentdetails::where('guid',$request->guid)->update(['pay_slips' => 0,'guid'=> $request->guid]);        
                    }
                }
            break;
            case "salary_slip":
                // if($request->hasFile('salary_slip')){
                 if($request->salary_slip != 'null'){
                    Joineedocument::where(['guid'=>$request->guid,'type'=> 10, 'check_box' => 1])->delete();
                    foreach($request->salary_slip as $letter){
                        $salarySlipFile = ['salary_slip' => $letter];           
                        $validator = Validator::make($salarySlipFile, [
                            'salary_slip' => 'mimes:jpeg,jpg,png,doc,docx,pdf', 
                            ]);
                        if ($validator->fails()) {
                        return response()->json($validator->errors());
                        }
                        
                        error_reporting(1);
        
                        $fileDetails = addslashes(file_get_contents($letter));
                        $fileProperties = File::mimeType($letter);
                        $file_name = $letter->getClientOriginalName();  
                        $addJoineeDocument= new Joineedocument();   
                        $addJoineeDocument->guid = $request->guid;
                        $addJoineeDocument->file_mime = $fileProperties;
                        $addJoineeDocument->file_data = $fileDetails;
                        $addJoineeDocument->file_name = $file_name;
                        $addJoineeDocument->type = 10;
                        $addJoineeDocument->save();         
                        Joineedocumentdetails::where('guid',$request->guid)->update(['salary_slip' => 1,'guid'=> $request->guid]);
                    }
                }else{
                    if($request->salary_certificate_check_box == true){
                        $reason = 'No Reason Specified';
                        if(!empty($request->salary_certificate_text_area)){
                            $reason = $request->salary_certificate_text_area;
                        }
                        $addJoineeDocument= Joineedocument::firstOrNew(array('type' => 10,'guid' =>  $request->guid));   
                        $addJoineeDocument->guid = $request->guid;
                        $addJoineeDocument->check_box = 1;
                        $addJoineeDocument->reason = $reason;                       
                        $addJoineeDocument->type = 10;
                        $addJoineeDocument->save();
                        Joineedocumentdetails::where('guid',$request->guid)->update(['salary_slip' => 0,'guid'=> $request->guid]);       
                    }
                }
            break;
            case "offer_letter":
                 if($request->offer_letter != 'null'){
                    Joineedocument::where(['guid'=>$request->guid,'type'=> 5, 'check_box' => 1])->delete();

                        foreach($request->offer_letter as $letter){
                        $offerLetterFile = ['offer_letter' => $letter];         
                        $validator = Validator::make($offerLetterFile, [
                            'offer_letter' => 'mimes:jpeg,jpg,png,doc,docx,pdf', 
                            ]);
                        if ($validator->fails()) {
                        return response()->json($validator->errors());
                        }
                        
                        error_reporting(1);
        
                        $fileDetails = addslashes(file_get_contents($letter));
                        $fileProperties = File::mimeType($letter);
                        $file_name = $letter->getClientOriginalName();  
                        $addJoineeDocument= new Joineedocument();   
                        $addJoineeDocument->guid = $request->guid;
                        $addJoineeDocument->file_mime = $fileProperties;
                        $addJoineeDocument->file_data = $fileDetails;
                        $addJoineeDocument->file_name = $file_name;
                        $addJoineeDocument->type = 5;
                        $addJoineeDocument->save();         
                        Joineedocumentdetails::where('guid',$request->guid)->update(['offer_letter' => 1,'guid'=> $request->guid]);
                    }
                }else{
                    if($request->offer_letter_check_box == true){
                        $reason = 'No Reason Specified';
                        if(!empty($request->offer_letter_text_area)){
                            $reason = $request->offer_letter_text_area;
                        }
                        $addJoineeDocument= Joineedocument::firstOrNew(array('type' => 5,'guid' =>  $request->guid)); 

                    // print_r($addJoineeDocument);
                    // die;  
                        $addJoineeDocument->guid = $request->guid;
                        $addJoineeDocument->check_box = 1;
                        $addJoineeDocument->reason = $reason;                       
                        $addJoineeDocument->type = 5;
                        $addJoineeDocument->save();
                        Joineedocumentdetails::where('guid',$request->guid)->update(['offer_letter' => 0,'guid'=> $request->guid]);     
                    }
                }
            break;
            case "relieve_letter":
                // if($request->hasFile('relieve_letter')){
                 if($request->relieve_letter != 'null'){
                    Joineedocument::where(['guid'=>$request->guid,'type'=> 6, 'check_box' => 1])->delete();
                    foreach($request->relieve_letter as $letter){
                        $relieveLetterFile = ['relieve_letter' => $letter];         
                        $validator = Validator::make($relieveLetterFile, [
                            'relieve_letter' => 'mimes:jpeg,jpg,png,doc,docx,pdf', 
                            ]);
                        if ($validator->fails()) {
                        return response()->json($validator->errors());
                        }
                        
                        error_reporting(1);
        
                        $fileDetails = addslashes(file_get_contents($letter));
                        $fileProperties = File::mimeType($letter);
                        $file_name = $letter->getClientOriginalName();  
                        $addJoineeDocument= new Joineedocument();   
                        $addJoineeDocument->guid = $request->guid;
                        $addJoineeDocument->file_mime = $fileProperties;
                        $addJoineeDocument->file_data = $fileDetails;
                        $addJoineeDocument->file_name = $file_name;
                        $addJoineeDocument->type = 6;
                        $addJoineeDocument->save();         
                        Joineedocumentdetails::where('guid',$request->guid)->update(['relieve_letter' => 1,'guid'=> $request->guid]);
                    }
                }else{
                    if($request->relieve_letter_check_box == true){
                        $reason = 'No Reason Specified';
                        if(!empty($request->relieve_letter_text_area)){
                            $reason = $request->relieve_letter_text_area;
                        }
                        $addJoineeDocument= Joineedocument::firstOrNew(array('type' => 6,'guid' =>  $request->guid));   
                        $addJoineeDocument->guid = $request->guid;
                        $addJoineeDocument->check_box = 1;
                        $addJoineeDocument->reason = $reason;                       
                        $addJoineeDocument->type = 6;
                        $addJoineeDocument->save();
                        Joineedocumentdetails::where('guid',$request->guid)->update(['relieve_letter' => 0,'guid'=> $request->guid]);       
                    }
                }
            break;
            case "experience_letter":
                // if($request->hasFile('experience_letter')){
                if($request->experience_letter != 'null'){
                    Joineedocument::where(['guid'=>$request->guid,'type'=> 7, 'check_box' => 1])->delete();
                    foreach($request->experience_letter as $letter){
                        $experienceLetterFile = ['experience_letter' => $letter];           
                        $validator = Validator::make($experienceLetterFile, [
                            'experience_letter' => 'mimes:jpeg,jpg,png,doc,docx,pdf', 
                            ]);
                        if ($validator->fails()) {
                        return response()->json($validator->errors());
                        }
                        
                        error_reporting(1);
        
                        $fileDetails = addslashes(file_get_contents($letter));
                        $fileProperties = File::mimeType($letter);
                        $file_name = $letter->getClientOriginalName();  
                        $addJoineeDocument= new Joineedocument();   
                        $addJoineeDocument->guid = $request->guid;
                        $addJoineeDocument->file_mime = $fileProperties;
                        $addJoineeDocument->file_data = $fileDetails;
                        $addJoineeDocument->file_name = $file_name;
                        $addJoineeDocument->type = 7;
                        $addJoineeDocument->save();         
                        Joineedocumentdetails::where('guid',$request->guid)->update(['experience_letter' => 1,'guid'=> $request->guid]);
                    }
                }else{
                    if($request->experience_letter_check_box == true){
                        $reason = 'No Reason Specified';
                        if(!empty($request->experience_letter_text_area)){
                            $reason = $request->experience_letter_text_area;
                        }
                        $addJoineeDocument= Joineedocument::firstOrNew(array('type' => 7,'guid' =>  $request->guid));   
                        $addJoineeDocument->guid = $request->guid;
                        $addJoineeDocument->check_box = 1;
                        $addJoineeDocument->reason = $reason;                       
                        $addJoineeDocument->type = 7;
                        $addJoineeDocument->save();
                        Joineedocumentdetails::where('guid',$request->guid)->update(['experience_letter' => 0,'guid'=> $request->guid]);        
                    }
                }
            break;
            }
       }
    
        $msg = "Joinee documents have been updated sucessfully";
        success_200(true, $msg);
    }

    public function download($id){

		$document = Joineedocument::find($id);
		// print_r($document);
		// die;

		$file_contents = stripslashes($document->file_data);
		// print_r($file_contents);
		// die;

		return response($file_contents)
			->header('Cache-Control', 'no-cache private')
			->header('Content-Description', 'File Transfer')
			->header('Content-Type', $document->mime_type)
			->header('Content-length', strlen($file_contents))
			->header('Content-Disposition', 'attachment; filename="' . $document->file_name . '"');
			// ->header('Content-Transfer-Encoding', 'binary');
    }

    // 1-photo,2-aadhar,3-pancard,4-passport,5-offer,6-relieve,7-experience,8-form_16,9-payslip,10-salary_cert
    public function delete($id){
        try{
        $get_document = Joineedocument::find($id);
        $type = $get_document->type;
        $guid = $get_document->guid;
        // print_r( $get_document );
        // echo $type;
        // echo $guid;
        // die;
        switch($type){
        case '1':
        Joineedocument::where('id',$id)->delete();
        Joineedocumentdetails::where('guid',$guid)->update(['photo'=>0]);
        break;
        case '2':
        Joineedocument::where('id',$id)->delete();
        Joineedocumentdetails::where('guid',$guid)->update(['aadhar_card'=>0]);
        break;
        case '3':
        Joineedocument::where('id',$id)->delete();
        Joineedocumentdetails::where('guid',$guid)->update(['pan_card'=>0]);
        break;
        case '4':
        Joineedocument::where('id',$id)->delete();
        Joineedocumentdetails::where('guid',$guid)->update(['passport'=>0]);
        break;
        case '5':
        Joineedocument::where('id',$id)->delete();
        $get_count = Joineedocument::where(['type'=>$type,'guid'=>$guid])->get();
        $count = $get_count->count();
        if($count > 0){
        Joineedocumentdetails::where('guid',$guid)->update(['offer_letter'=>1]);
        }else{
        Joineedocumentdetails::where('guid',$guid)->update(['offer_letter'=>0]);
        }
        break;
        case '6':
        Joineedocument::where('id',$id)->delete();
        $get_count = Joineedocument::where(['type'=>$type,'guid'=>$guid])->get();
        $count = $get_count->count();
        if($count > 0){
        Joineedocumentdetails::where('guid',$guid)->update(['relieve_letter'=>1]);
        }else{
        Joineedocumentdetails::where('guid',$guid)->update(['relieve_letter'=>0]);
        }
        break;
        case '7':
        Joineedocument::where('id',$id)->delete();
        $get_count = Joineedocument::where(['type'=>$type,'guid'=>$guid])->get();
        $count = $get_count->count();
        if($count > 0){
        Joineedocumentdetails::where('guid',$guid)->update(['experience_letter'=>1]);
        }else{
        Joineedocumentdetails::where('guid',$guid)->update(['experience_letter'=>0]);
        }
        break;
        case '8':
        Joineedocument::where('id',$id)->delete();
        Joineedocumentdetails::where('guid',$guid)->update(['form_16'=>0]);
        break;
        case '9':
        Joineedocument::where('id',$id)->delete();
        $get_count = Joineedocument::where(['type'=>$type,'guid'=>$guid])->get();
        $count = $get_count->count();
        if($count > 0){
        Joineedocumentdetails::where('guid',$guid)->update(['pay_slips'=>1]);
        }else{
        Joineedocumentdetails::where('guid',$guid)->update(['pay_slips'=>0]);
        }
        break;
        case '10':
        Joineedocument::where('id',$id)->delete();
        // die;
        // echo $type;
        // echo $guid;
        $get_count = Joineedocument::where(['type'=>$type,'guid'=>$guid])->get();
        // print_r($get_count);die;
        $count = $get_count->count();
        if($count > 0){
        Joineedocumentdetails::where('guid',$guid)->update(['salary_slip'=>1]);
        }else{
        Joineedocumentdetails::where('guid',$guid)->update(['salary_slip'=>0]);
        }
        break;
        }
        success_200(true,$id,'Document deleted successfully');
        }catch(\Exception $e){
        error_404(false,$e);
        }
        
        }
}
