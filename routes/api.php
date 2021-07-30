<?php

use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('candidates/{id}/professional-documents/download', 'CandidateController@download');
Route::get('downloadresume/{id}', 'CandidateController@downloadResume');
Route::get('joineepersonaldocdownload/{id}', 'JoineePersonalInfoController@joineedocdownload');
Route::post('login', 'ApiController@login');
Route::post('register', 'ApiController@register');
Route::apiResource('updateUserDetails','UserDetailsController');

// Route::post('employeedetail/{guid}', 'EmployeedetailsController@showCandidateDetail');
Route::get('joinee/checkurl/{guid}', 'JoineePersonalInfoController@index');
Route::get('joinee/checkDetails/{guid}', 'JoineePersonalInfoController@checkDetails');
Route::get('joinee/checkDetailLinkStatus/{guid}', 'JoineePersonalInfoController@checkDetailLinkStatus');
Route::post('joinee/addJoineeInfo', 'JoineePersonalInfoController@addJoinee');
Route::post('joinee/addpersonalinfo', 'JoineePersonalInfoController@addJoineePersonalInfo');
Route::post('joinee/addpersonalreferenceinfo', 'JoineePersonalInfoController@addJoineePersonalRefrence');
Route::post('joinee/addprofessionalreferenceinfo', 'JoineePersonalInfoController@addJoineeProfessionalRefrence');
Route::post('joinee/addpreviouscompanyinfo', 'JoineePersonalInfoController@addJoineePreviousCompany');
Route::post('joinee/adddocumentinfo', 'JoineePersonalInfoController@addJoineeDocuments');
Route::post('joinee/deleteDoc/{id}', 'JoineePersonalInfoController@delete');
Route::get('joineedetail/{guid}', 'EmployeedetailsController@index');
Route::get('candidatedetail/{guid}', 'EmployeedetailsController@showCandidateDetail');


Route::group(['middleware' => 'auth.jwt'], function () {
	Route::post('logout', 'ApiController@logout');
	Route::get('user', 'ApiController@getAuthUser');
	Route::get('users', 'ApiController@getUsersForList');

	Route::get('document_titles', 'CandidateController@listDocuments');
	Route::get('candidates', 'CandidateController@listCandidates');
	Route::get('candidates/{id}', 'CandidateController@showCandidate');
	Route::post('candidates/add', 'CandidateController@addCandidate');
	Route::post('resend-mail', 'CandidateController@ReSendMail');
	Route::get('get-failed-mail/{candiadate_id}', 'CandidateController@getFailedMail');
	Route::post('candidates/{id}/update', 'CandidateController@updateCandidate');
	Route::post('candidates/{id}/update-detail', 'CandidateController@updateCandidateDetail');
	Route::post('candidates/{id}/update-sysReq-detail', 'CandidateController@updateCandidateSysReqDetail');
	Route::post('candidates/{id}/updateOnboarding', 'CandidateController@updateOnboarding');
	Route::delete('candidates/{id}', 'CandidateController@deleteCandidate');
	Route::post('candidates/{id}/professional-documents/add', 'CandidateController@add');
	Route::post('candidates/{id}/professional-documents/update', 'CandidateController@update');
	Route::post('candidates/{id}/professional-documents/deleteall', 'CandidateController@deleteAll');
	Route::post('candidates/{id}/professional-documents/deletesingle', 'CandidateController@deleteSingle');
	// Route::get('candidates/{id}/professional-documents/download', 'CandidateController@download');
	Route::get('candidates/{id}/professional-documents', 'CandidateController@index');
	Route::get('candidates/{id}/professional-documents/getDetails', 'CandidateController@getJoineeDocDetails');
	Route::post('candidates/{id}/assessment/add', 'TaskController@add');
	Route::post('candidates/{id}/assessment/update', 'TaskController@update');
	Route::get('candidates/{id}/assessment/', 'TaskController@index');
	Route::get('candidates/{id}/id-card/', 'IDCardController@index');
	Route::post('candidates/{id}/id-card/add', 'IDCardController@add');
	Route::post('candidates/{id}/id-card/update', 'IDCardController@update');
	Route::delete('candidates/{id}/id-card/delete', 'IDCardController@delete');
	Route::post('candidates/{id}/techinical-task', 'CandidateController@addTechinicalTask');
	Route::get('candidates/{id}/get-techinical-task', 'CandidateController@getTechinicalTask');
	Route::post('candidates/{id}/techinical-task/delete', 'CandidateController@deleteTechinicalTask');
	Route::post('candidates/{id}/techinical-task/update', 'CandidateController@updateTechinicalTask');
	Route::get('candidates-monthcount', 'CandidateController@monthCandidates');
	Route::get('candidates-count', 'CandidateController@candidatesCount');
	// Route::post('employeedetail', 'EmployeedetailsController@index');

	Route::post('factsheet/add', 'FactSheetController@add');
	Route::get('factsheet/show/{id}', 'FactSheetController@show');
	Route::put('factsheet/update/{id}', 'FactSheetController@update');
	Route::get('factsheet/getState', 'FactSheetController@getState');
	Route::get('factsheet/getTown', 'FactSheetController@getTown');

	Route::get('get-user-roles', 'Role\RoleController@getUserRoles');
	Route::get('get-particular-user-roles/{id}', 'Role\RoleController@getParticularUserRoles');
	Route::post('add-designation', 'Role\RoleController@addDesignation');
	Route::get('get-particular-designation/{id}', 'Role\RoleController@getParticularDesignation');
	Route::post('update-designation', 'Role\RoleController@updateDesignation');
	Route::post('add-user-role', 'Role\RoleController@addUserRole');
	Route::post('update-user-role', 'Role\RoleController@updateUserRole');
	Route::post('delete-user-role', 'Role\RoleController@deleteUserRole');
	Route::get('get-permissions', 'Role\PermissionController@getPermissions');
	Route::get('get-role-permissions/{id}', 'Role\PermissionController@getRolePermission');
	Route::post('add-role-permission', 'Role\PermissionController@addRolePermission');
	Route::post('update-role-permission', 'Role\PermissionController@updaterolepermissions');

	Route::get('get-designation', 'DepartmentController@designation');
	Route::get('get-departments', 'DepartmentController@department');
	Route::get('get-department-designation', 'DepartmentController@departmentDesignation');
	Route::get('particular-department-designation/{id}', 'DepartmentController@particularDepartmentDesignation');
	Route::get('manage-departments', 'DepartmentController@managedepartmentlist');
	Route::post('add-department-designation', 'DepartmentController@AddDepartmentDesignation');
	Route::post('update-department-designation', 'DepartmentController@UpdateDepartmentDesignation');

	// Route::apiResource('updateUserDetails','UserDetailsController');

	Route::apiResource('departments', 'DepartmentController');
	Route::apiResource('roles', 'Role\RoleController');
	Route::apiResource('permission', 'Role\PermissionController');
	Route::apiResource('leads', 'LeadController');
	Route::apiResource('requirement', 'RequirementController');
});


//Joinee details filled by Candidate
Route::post('joinee/add', 'JoineePersonalInfoController@addJoinee');

Route::get('joinee/generateUUID', 'JoineePersonalInfoController@generateUUID');
//Route::post('joinee/{id}', 'JoineePersonalInfoController@validateGUID');

	
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
