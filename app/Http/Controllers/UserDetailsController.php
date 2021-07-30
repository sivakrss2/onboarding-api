<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
//use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Synergysystem;
use App\User;
use Redirect;
use Session;

use Auth;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use App\UserDetails;
use App\UserDesignation;
use App\Lead;

//use DB;

class UserDetailsController extends Controller
{
	//protected $user;

	public function __construct(Request $request)
	{

	}
	public function index()
		{
			// $array = array(2,3,4,5,8,10,17,18,23,25,28,31,35,37,38,39,42,46,74,78,79,94,95,105,107,112,116,117,118,120,122); 
			$array = array(2,3,8,10,17,18,25,35,36,46,49,56,90,102,104,105,116,118,120,125,126); 
			foreach($array as $a)
			{
			$user_details_count = UserDetails::where([['IsActive', '=', true],['DesignationICode','=',$a]])->count();
			print_r($user_details_count);die;
				if($user_details_count>0){
					$user_details = UserDetails::where([['IsActive', '=', true],['DesignationICode','=',$a]])->get();
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
			$msg = 'User test details added successfully';
			success_200(true,'',$msg);
		}
}
