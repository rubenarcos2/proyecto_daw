<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Create a new PermissionController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => 'index']);
        $this->middleware('permission:permission-list', ['only' => ['show']]);
        $this->middleware('permission:permission-create', ['only' => ['store']]);
        $this->middleware('permission:permission-edit', ['only' => ['userPermissions']]);
        $this->middleware('permission:permission-delete', ['only' => ['userAssignRole']]);
    }

    /**
     * Display a listing of the resource of permissions.
     */
    public function index()
    {
        try{
            $permissions = Permission::all();
            return response()->json($permissions);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource of permission.
     */
    public function show(string $id)
    {        
        try{
            $permission = Permission::find($id);
            return response()->json($permission);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the user permissions.
     */
    public function userPermissions(string $id)
    {        
        try{
            $user = User::find($id);
            $permissions = $user->getAllPermissions();
            return response()->json($permissions);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Assign the specified permissions to user in storage.
     *
     * @param  \Illuminate\Http\Request  $request (permission list)
     * @param  int  $id (user id)
     * @return \Illuminate\Http\Response
     */
    public function userAssignRole(Request $request, $id)
    {
        try{
            $this->validate($request, [
                'permissions' => 'required',
            ]);
            $user = User::find($id);
            $roleId = $user->roles->first()->id;
            $role = Role::find($roleId);
            //Revoke & add new permissions
            //Input example -> {"permissions":["product-list","config-list","config-edit"]}
            $role->syncPermissions(json_decode($request->permissions,true));
            
            return response()->json(['message' => 'Se han asignado los permisos al usuario']);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
