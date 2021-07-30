<?php

namespace App\Http\Controllers\Role;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Permission;
use JWTAuth;
use Validator;
use Auth;
use DB;
use App\Role;

class PermissionController extends Controller
{
	protected $user;

	/**
	 * PermissionController constructor.
	 * @param Request $request
	 */
	public function __construct(Request $request)
	{
		if (!isset($request->token)) {
			return response()->json(['status' => false]);
		}
		$this->user = JWTAuth::parseToken()->authenticate();
	}

	/**
	 * @param $roleId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function show($roleId)
	{
		$role = Role::where('id', '=', $roleId)->first();

		$permissionList = $role->permissions;

		// $response = error_404();

		if (!empty($permissionList)) {
			$response = http_200(true, 'Success', $permissionList);
		}
		return $response;
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getPermissions()
	{
		$permissions = Permission::all();
		$response = error_401('No Permissions found.');
		if ($permissions) {
			$response = http_200(true, 'Success', $permissions);
		}
		return $response;
	}

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function addRolePermission(Request $request)
	{
		
		$data = $request->all();
		
		$permissionId = [];
	
		$validator = Validator::make($request->all(), ["permission_id" => "required","role_name" => "required"]);

		if ($validator->fails()) {
			return http_200(false, 'Validation Error', $validator->errors());
		}

		// Creating new role
		$newRole = $data['role_name'];
		$role = new Role;
		$role->name = $newRole;
		$role->save();
		$roleId = $role->id;		


		foreach ($data['permission_id'] as $d) {			
			array_push($permissionId, $d['id']);
		}
		
		return $this->createRolePermission($permissionId, $roleId);
	}

	/**
	 * @param $permissionId
	 * @param $roleId
	 * @return \Illuminate\Http\JsonResponse
	 */
	private function createRolePermission($permissionId, $roleId)
	{
		foreach($permissionId as $id){
			DB::table('permission_role')
				->insert(['permission_id' => $id, 'role_id' => $roleId]
			);
		}
		return http_200(true, 'User Role created successfully', '');	
	}


	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */

	 public function updaterolepermissions(Request $request){
		// echo '<pre>';
		// print_r($request->all());
		// die;

		$data = $request->all();
		$roleId = $data['role_id'];
		$roleName = $data['role_name'];			
		Role::where('id',$roleId)->update(['name' => $roleName]);

		$permissionId = [];
		$oldPermissionId = [];
		foreach ($data['permission_id'] as $d) {			
			array_push($permissionId, $d['id']);
		}
		// print_r($permissionId);

		$getAssignedPermissions = DB::table('permission_role')
									->select('permission_id')
									->where('role_id', $roleId)
									->get();

		// print_r($getAssignedPermissions);
		// die;
		foreach($getAssignedPermissions as $value){
			array_push($oldPermissionId,$value->permission_id);
		}

		$addNewpermissions = array_diff($permissionId,$oldPermissionId);
		$removeOldPermissions = array_diff($oldPermissionId,$permissionId);

		if(!empty($addNewpermissions)){

			foreach($addNewpermissions as $newId){
				DB::table('permission_role')
				->insert(['permission_id' => $newId, 'role_id' => $roleId]);
			}
			return http_200(true, 'Role Permissions Updated Successfully', '');

		}elseif(!empty($removeOldPermissions)){

			foreach($removeOldPermissions as $oldId){
				DB::table('permission_role')
					->where('permission_id', $oldId)
					->where('role_id', $roleId)
					->delete();
			}
			return http_200(true, 'Role Permissions Updated Successfully', '');
		}else{
			return http_200(true, 'Role Permissions Updated Successfully', '');
		}
		
		// print_r($removeOldPermissions);
		// die;
	 }

	/**
	 * @param $role
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getRolePermission($id)
	{
		$rolePermissions = DB::table('roles')
					->join('permission_role', 'roles.id' , '=', 'permission_role.role_id')
					->join('permissions', 'permission_role.permission_id' , '=', 'permissions.id')
					->where('roles.id', $id)
					->get(['permission_role.permission_id','permission_role.role_id as permisssion_role_id','roles.id as role_id' ,'roles.name as role_id_name','permissions.id','permissions.name']);
		
		if (!empty($rolePermissions)) {
			$response = http_200(true, 'Success', $rolePermissions);
		}
		else{
			$response = error_404(false, 'No role permission found.');
		}
		return $response;
	}

}