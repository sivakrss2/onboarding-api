<?php

namespace App\Http\Controllers\Role;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Validator;
use Auth;
use App\Role;

class RoleController extends Controller
{
	protected $user;

	/**
	 * RoleController constructor.
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
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index(Request $request)
	{
		$sortColumn = isset($request->sort_column) ? $request->input('sort_column') : '';
		$sortOrder = isset($request->sort_order) ? $request->input('sort_order') : '';
		$searchText = isset($request->search_txt) ? $request->input('search_txt') : '';
		$length = isset($request->length) ? $request->input('length') : '';
		switch (true) {
			case ($sortColumn !== '' && $sortOrder !== '' && $sortColumn !== 'undefined'):
				$sort_list = Role::orderBy($sortColumn, $sortOrder)->paginate($length);
				$responseJson = response()->json($sort_list);
				break;
			case ($searchText !== ''):
				$responseJson = $this->getRoleListBySearchText($searchText);
				break;
			default:
				$responseJson = $this->getRoleList($length);
				break;
		}
		return $responseJson;
	}

	/**
	 * @param $searchText
	 * @return \Illuminate\Http\JsonResponse
	 */
	private function getRoleListBySearchText($searchText)
	{
		$searchList = Role::where('name', 'LIKE', '%' . $searchText . '%')->orWhere('description', 'LIKE', '%' . $searchText . '%')
			->orWhere(' display_name ', 'LIKE', '%' . $searchText . '%')->get();

		$response = error_404();

		if (count($searchList) > 0) {
			$response = http_200(true, 'Success', $searchList);
		}
		return $response;
	}

	/**
	 * @param $length
	 * @return \Illuminate\Http\JsonResponse
	 */
	private function getRoleList($length)
	{
		$roleList = Role::select(["id", "name"])->paginate($length);

		if (!empty($roleList)) {
			$response = http_200(true, 'Success', $roleList);
		}
		return $response;
	}

	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function show($id)
	{
		$roleData = Role::find($id);
		$response = http_200(true, 'Success', $roleData);

		if (!$roleData) {
			$response = error_404();
		}
		return $response;
	}

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store(Request $request)
	{
		$validator = $this->validateRoleInputs($request);

		if ($validator->fails()) {
			return http_200(false, 'Validation Error', $validator->errors());
		}

		return $this->createRole($request);
	}

	/**
	 * @param $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	private function createRole($request)
	{
		DB::beginTransaction();

		try {
			$role = new Role();
			$role->name = $request->input('name');
			$role->description = $request->input('description');
			$role->display_name = $request->input('display_name');
			$role->created_at = date('Y-m-d H:i:s');
			$role->updated_at = date('Y-m-d H:i:s');
			$role->save();
			$response = http_201('Role entry has been created successfully', $role);

			DB::commit();

		} catch (\Exception $e) {
			DB::rollback();
			$response = response()->json(['error' => $e->getMessage()], 500);
		}

		return $response;
	}

	/**
	 * @param $request
	 * @return mixed
	 */
	private function validateRoleInputs($request)
	{
		return Validator::make($request->all(), [
			'name' => 'required|max:255',
			'description' => 'required|max:255',
			'display_name' => 'required|max:255',
		]);
	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update(Request $request, $id)
	{
		$role = Role::find($id);

		if (!$role) {
			return error_404();
		}

		$validator = $this->validateRoleInputs($request);

		if ($validator->fails()) {
			return http_200(false, 'Validation Error', $validator->errors());
		}

		return $this->updateRole($request, $id);
	}

	/**
	 * @param $request
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	private function updateRole($request, $id)
	{
		Role::where('id', $id)->update([
			'name' => $request->input('name'),
			'description' => $request->input('description'),
			'display_name' => $request->input('display_name')
		]);

		$role = Role::where('id', $id)->first();

		return http_201('Role entry has been updated successfully', $role);
	}

	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function destroy($id)
	{
		$roleData = Role::find($id);

		if (!$roleData) {
			return error_404();
		}

		$role = Role::where('id', '=', $id)->first();
		$role->delete();

		return http_200(true, 'Role entry has been deleted successfully', '');
	}

	public function getUserRoles()
	{
		// $user = User::find($id);

		$user = DB::table('role_user')
						->join('users', 'role_user.user_id' , '=', 'users.id')
						->join('roles', 'role_user.role_id','=','roles.id')
						->orderBy('roles.id','ASC')
						->get(['role_user.id','role_user.user_id','role_user.role_id','users.name as user_name','roles.name as role_name', 'users.full_name as full_name']);
	
		$response = error_401('No users found.');

		if ($user) {
			$response = http_200(true, 'Success', $user);
		}

		return $response;
	}

	
	public function getParticularUserRoles($id)
	{
		// $user = User::find($id);
		// $id = $request->id;
		$user = DB::table('role_user')
						->join('users', 'role_user.user_id' , '=', 'users.id')
						->join('roles', 'role_user.role_id','=','roles.id')
						->where('role_user.id', $id)
						->get(['role_user.id','role_user.user_id','role_user.role_id','users.name as user_name','roles.name as role_name']);
	
		$response = error_401('No users found.');

		if ($user) {
			$response = http_200(true, 'Success', $user);
		}

		return $response;
	}

	public function addUserRole(Request $request) {
		Validator::make($request->all(), [
			'user_id' => 'required',
			'role_id' => 'required'
		]);
		$user_id = $request->user_id;
		$role_id = $request->role_id;
		$data = DB::table("role_user")->insert([
					"user_id" => $user_id,
					"role_id" => $role_id,
				]);
		return http_200(true, 'User Role created successfully', '');
	}

	public function updateUserRole(Request $request) {
		Validator::make($request->all(), [
			'id' => 'required',
			'role_id' => 'required'
		]);
		$id = $request->id;
		$role_id = $request->role_id;
		$data = DB::table("role_user")->where(["id" => $id])->update([
					"role_id" => $role_id,
				]);
		return http_200(true, 'User Role updated successfully', '');
	}

	public function deleteUserRole(Request $request)
	{
		$id = $request->id;
		   $userRole = DB::table('role_user')
					->where('id', $id)
					->delete();
		if (!$userRole) {
			$msg = 'Sorry, User role with id ' . $id . ' cannot be found or Cannot process the request';
			error_404(false, $msg);
			die;
		}
		$user = DB::table('role_user')
						->join('users', 'role_user.user_id' , '=', 'users.id')
						->join('roles', 'role_user.role_id','=','roles.id')
						->orderBy('roles.id','ASC')
						->get(['role_user.user_id','role_user.role_id','users.name as user_name','roles.name as role_name']);

		$msg = 'User role has been deleted';
		success_200(true, $user, $msg);
	}

	
	public function getParticularDesignation($id)
	{
		// $user = User::find($id);
		// $id = $request->id;
		$designation = DB::table('designations')
						->where('designations.id', $id)
						->get();
	
		$response = error_401('No Designation found.');

		if ($designation) {
			$response = http_200(true, 'Success', $designation);
		}

		return $response;
	}

	public function addDesignation(Request $request) {
		Validator::make($request->all(), [
			'designation_name' => 'required'
		]);
		$data = DB::table("designations")->insert([
					"designation_name" => $request->designation_name,
				]);

	
		return http_200(true, 'Designation created successfully', '');
	}

	public function updateDesignation(Request $request) {
		Validator::make($request->all(), [
			'id' => 'required',
			'designation_name' => 'required'
		]);
		$id = $request->id;
		$designation_name = $request->designation_name;
		$data = DB::table("designations")->where(["id" => $id])->update([
					"designation_name" => $designation_name,
				]);
		return http_200(true, 'Designation updated successfully', '');
	}

	

}
