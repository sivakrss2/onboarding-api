<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;

class EmployeedetailsController extends Controller
{
    //
    // public function index(Request $request,$msg = ''){
        public function index(Request $request,$msg = ''){

            // echo "hii123";
            // echo $request->guid;
            // // echo $guid;
            // die;
        
        $data = [];
        $candidate_details = DB::table('candidates AS C')
                            ->join('designations AS DE','DE.id','=','C.designation_id')
                            ->join('departments AS DP','DP.id','=','C.department_id')
                            ->select('C.*','DE.designation_name','DP.department')
                            ->where('C.id','=',$request->candidate_id)
                            ->get()
                            ->toArray();

                $company_details = [];
                    foreach($candidate_details as $key => $value){
                        $company_details['emplpoyee_number'] = $value->id;
                        $company_details['name'] = $value->name;
                        $company_details['department'] = $value->department;
                        $company_details['designation'] = $value->designation_name;                       
                    }

                $joinee_details = DB::table('joinee_personal_info')
                                ->select('*')
                                ->where('guid','=',$request->guid)
                                ->get();

                $joinee_info_id = DB::table('joinee_personal_info')
                                ->select('joinee_id')
                                ->where('guid','=',$request->guid)
                                ->get();
            if(!$joinee_info_id->isEmpty()){
                $joinee_child_info =  DB::table('joinee_child_info')                                
                                                    ->select('*')
                                                    ->where('joinee_id','=',$joinee_info_id[0]->joinee_id)
                                                    ->get();
            
                                        $data['joinee_child_info'] = $joinee_child_info;
            }

                $joinee_personal_reference = DB::table('joinee_personal_reference')                                
                                            ->select('*')
                                            ->where('guid','=',$request->guid)
                                            ->get(); 
                                    
                $joinee_professional_reference = DB::table('joinee_professional_reference')                                
                                            ->select('*')
                                            ->where('guid','=',$request->guid)
                                            ->get();                    

                $joinee_previous_company = DB::table('joinee_previous_company')                                
                                            ->select('*')
                                            ->where('guid','=',$request->guid)
                                            ->get(); 

                $joinee_documents = DB::table('joinee_documents')                                
                                            ->select('id','guid','file_name','check_box','reason','type')
                                            ->where('guid','=',$request->guid)
                                            ->get(); 

                $data['joinee_personal_info'] = $joinee_details;
                $data['joinee_personal_reference'] = $joinee_personal_reference;
                $data['joinee_professional_reference'] = $joinee_professional_reference;
                $data['joinee_previous_company'] = $joinee_previous_company;
                $data['joinee_documents'] = $joinee_documents;
                    
                    success_200(true, $data , $msg);
                    

    }

    public function showCandidateDetail($guid)
	{
		$candidate = DB::table('candidates')
                    ->where('candidates.guid', $guid)
                    ->get(['candidates.*']);

        if(!$candidate){
            $msg = 'Candidate cannot be found';
            error_404(false,$msg);
            die;
        }
        success_200(true,$candidate);
	}


}
