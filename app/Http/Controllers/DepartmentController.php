<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
use JWTAuth;
use Validator;
use Auth;
use App\Department;
use App\Designation;
use DB;

class DepartmentController extends Controller
{
    protected $user;

    public function __construct(Request $request)
    {
        // if(!isset($request->token)){
        //     return response()->json(['success' => false]);
        // }
        // $this->user = JWTAuth::parseToken()->authenticate();
    }

    protected $validationRules = [
        'department' => 'required|max:255',
        'description' => 'required|max:255',
    ];

    public function index(Request $request){
        $sort = $request->sort;
        $order = $request->order;
        $search = $request->search;
        $limit = $request->limit;

        $departmentlist = Department::query();

        $department_results = $departmentlist->get();

        if(count($department_results) === 0){
            $msg = 'No Records found';
            error_404(false,$msg);
            die;
        }
        if($search){
            $departmentlist = $departmentlist->where('department','LIKE','%'.$search.'%')
                              ->orWhere('description','LIKE','%'.$search.'%');
            $search_results = $departmentlist->get();

            if(count($search_results) === 0){
                $msg = 'No search results found for the query '.$search;
                error_404(false,$msg);
                die;
            }
        }

        if($sort && $order){
            $list = $departmentlist->orderBy($sort,$order)->paginate($limit);
        } else {
            $list = $departmentlist->orderBy('id','ASC')->paginate($limit);
        }
		return http_201('success', $list);
    }

    public function designation(Request $request){
        $sort = $request->sort;
        $order = $request->order;
        $search = $request->search;
        $limit = $request->limit;

        // $designationlist = Designation::query();

        // $designation_results = $designationlist->get();

        $designationlist = DB::table('designations')->select('*', 'id as designation_id')->get();

        if(count($designationlist) === 0){
            $msg = 'No Records found';
            error_404(false,$msg);
            die;
        }
        if($search){
            $designationlist = $designationlist->where('designation','LIKE','%'.$search.'%')
                              ->orWhere('description','LIKE','%'.$search.'%');
            $search_results = $designationlist->get();

            if(count($search_results) === 0){
                $msg = 'No search results found for the query '.$search;
                error_404(false,$msg);
                die;
            }
        }

        if($sort && $order){
            $list = $designationlist->orderBy($sort,$order)->paginate($limit);
        } else {
            $list = $designationlist;
        }
        return http_201('success', $list);
    }

    public function department(Request $request){
        $sort = $request->sort;
        $order = $request->order;
        $search = $request->search;
        $limit = $request->limit;

        $departmentlist = DB::table('departments')->get();

        if(count($departmentlist) === 0){
            $msg = 'No Records found';
            error_404(false,$msg);
            die;
        }
        if($search){
            $departmentlist = $departmentlist->where('department','LIKE','%'.$search.'%')
                              ->orWhere('description','LIKE','%'.$search.'%');
            $search_results = $departmentlist->get();

            if(count($search_results) === 0){
                $msg = 'No search results found for the query '.$search;
                error_404(false,$msg);
                die;
            }
        }

        if($sort && $order){
            $list = $departmentlist->orderBy($sort,$order)->paginate($limit);
        } else {
            $list = $departmentlist;
        }
        return http_201('success', $list);
    }


    public function show($id){
        $department_list = Department::find($id);

        if(!$department_list){
            $msg = 'Department with id '.$id.' cannot be found';
            error_404(false,$msg);
            die;
        }
		return http_201('success', $department_list);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),$this->validationRules);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

		DB::beginTransaction();
		try {
			$departments = new Department();
			$departments->department = $request->department;
			$departments->description = $request->description;
			$departments->created_by = Auth::user()->id;
			$departments->updated_by = Auth::user()->id;
			$departments->save();
			DB::commit();
			$msg = 'Department is created successfully';
			$response =  http_201($msg, $departments);
		} catch (\Exception $e){
			DB::rollback();
			$response = response()->json(['error' => $e->getMessage()], 500);
		}
		return $response;
    }

    public function update(Request $request,$id)
    {
        $userid = Auth::user()->id;
        $department = Department::find($id);

        if(!$department){
            $msg = 'Sorry, Department with id '.$id.' cannot be found';
            error_404(false,$msg);
            die;
        }

        $validator = Validator::make($request->all(),$this->validationRules);
        if($validator->fails()){
            return response()->json($validator->errors());
        }

        $dept_name = $request->department;
        $dept_desc = $request->description;
        $updated =  $department->update([
                        'department'    =>  $dept_name,
                        'description'   =>  $dept_desc,
                        'updated_by'    =>  $userid
                    ]);

        $msg = 'Department is updated successfully';
		return  http_201($msg, $department);
    }

    public function destroy($id){
        $department = Department::find($id);

        if(!$department){
            $msg = 'Sorry, Department with id '.$id.' cannot be found';
            error_404(false,$msg);
            die;
        }

        $deleted = $department->delete();
        $msg = 'Department is deleted';
        success_200(true,$msg,'');
    }

    public function departmentDesignation(Request $request){
        $sort = $request->sort;
        $order = $request->order;
        $search = $request->search;
        $limit = $request->limit;

        $department_designation_list = DB::table('department_designation')
                                ->join('departments', 'department_designation.department_id' , '=', 'departments.id')
                                ->join('designations', 'department_designation.designation_id','=','designations.id')
                                ->select(['department_designation.id','departments.id as department_id','departments.department as department_name'])
                                ->groupBy('departments.department')
                                ->distinct()->get();
                                // ->get(['department_designation.id','department_designation.department_id','department_designation.designation_id','departments.department as department_name','designations.designation_name as designation_name']);

        if(count($department_designation_list) === 0){
            $msg = 'No Records found';
            error_404(false,$msg);
            die;
        }

        if($search){
            $department_designation_list = $department_designation_list->where('department','LIKE','%'.$search.'%')
                            ->orWhere('description','LIKE','%'.$search.'%');
            $search_results = $department_designation_list->get();

            if(count($search_results) === 0){
                $msg = 'No search results found for the query '.$search;
                error_404(false,$msg);
                die;
            }
        }

        if($sort && $order){
            $list = $department_designation_list->orderBy($sort,$order)->paginate($limit);
        } else {
            $list = $department_designation_list;
        }
        return http_201('success', $list);
    }

    public function particularDepartmentDesignation($id){

        $department_designation_list = DB::table('department_designation')
                                ->where('department_designation.department_id', $id)
                                ->join('departments', 'department_designation.department_id' , '=', 'departments.id')
                                ->join('designations', 'department_designation.designation_id','=','designations.id')
                                ->get(['department_designation.id','department_designation.department_id','department_designation.designation_id','departments.department as department_name','designations.designation_name as designation_name']);

        if(!$department_designation_list){
            $msg = 'department designation with id '.$id.' cannot be found';
            error_404(false,$msg);
            die;
        }

        return http_201('success', $department_designation_list);
    }

    public function managedepartmentlist(Request $request){
        $sort = $request->sort;
        $order = $request->order;
        $search = $request->search;
        $limit = $request->limit;

        $departmentDesignation = DB::table('department_designation')
                                ->join('departments', 'department_designation.department_id' , '=', 'departments.id')
                                ->join('designations', 'department_designation.designation_id','=','designations.id')
                                ->select('departments.id')
                                ->groupBy('departments.department')
                                ->distinct()->get();

        foreach ($departmentDesignation as $department_id) {
            $data[] = $department_id->id;
        }

        $departmentlist = DB::table('departments')->whereNotIn('id', $data)->get();

        if(count($departmentlist) === 0){
            $msg = 'No Records found';
            error_404(false,$msg);
            die;
        }
        if($search){
            $departmentlist = $departmentlist->where('department','LIKE','%'.$search.'%')
                              ->orWhere('description','LIKE','%'.$search.'%');
            $search_results = $departmentlist->get();

            if(count($search_results) === 0){
                $msg = 'No search results found for the query '.$search;
                error_404(false,$msg);
                die;
            }
        }

        if($sort && $order){
            $list = $departmentlist->orderBy($sort,$order)->paginate($limit);
        } else {
            $list = $departmentlist;
        }
        return http_201('success', $list);
    }

    /**
	 * @param $permissionId
	 * @param $roleId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function AddDepartmentDesignation(Request $request)
	{
        $data = $request->all();
		foreach($data as $value){
			DB::table('department_designation')
				->insert(['department_id' => $value['department_id'], 'designation_id' => $value['designation_id']]
			);
		}
		return http_200(true, 'Department Designation created successfully', '');	
    }
    
    /**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */

	 public function UpdateDepartmentDesignation(Request $request){
		// echo '<pre>';
		// print_r($request->all());
		// die;

        $data = $request->all();
        // print_r($data[0]['department_id']);die;
		// $roleId = $data['role_id'];
		// $roleName = $data['role_name'];			
		// Role::where('id',$roleId)->update(['name' => $roleName]);

		$departmentDesignation = [];
        $oldDepartmentDesignation = [];
        
		foreach ($data as $d) {			
            array_push($departmentDesignation, $d['designation_id']);
		}
		// print_r($departmentDesignation);die;

		$getAssignedDesignation = DB::table('department_designation')
									->select('designation_id')
									->where('department_id', $data[0]['department_id'])
									->get();

		// print_r($getAssignedDesignation);
		// die;
		foreach($getAssignedDesignation as $value){
			array_push($oldDepartmentDesignation,$value->designation_id);
		}

        // print_r($oldDepartmentDesignation);die;
        
		// $addNewpermissions = array_diff($permissionId,$oldPermissionId);
        // $removeOldPermissions = array_diff($oldPermissionId,$permissionId);
        
        $addNewDepartmentDeignation = array_diff($departmentDesignation,$oldDepartmentDesignation);
        $removeOldDepartmentDeignation = array_diff($oldDepartmentDesignation,$departmentDesignation);
        // print_r($addNewDepartmentDeignation);
        // echo "hi";
        // print_r($removeOldDepartmentDeignation);
        // die;

		if(!empty($addNewDepartmentDeignation)){

			foreach($addNewDepartmentDeignation as $newId){
				DB::table('department_designation')
				->insert(['department_id' => $data[0]['department_id'], 'designation_id' => $newId]);
            }
			return http_200(true, 'Department Designation Updated Successfully', '');

        }
        elseif(!empty($removeOldDepartmentDeignation)){

			foreach($removeOldDepartmentDeignation as $oldId){
				DB::table('department_designation')
					->where('designation_id', $oldId)
					->where('department_id', $data[0]['department_id'])
					->delete();
			}
			return http_200(true, 'Department Designation Updated Successfully', '');
		}else{
			return http_200(true, 'Department Designation Updated Successfully', '');
		}
		
		// print_r($removeOldPermissions);
		// die;
	 }
}