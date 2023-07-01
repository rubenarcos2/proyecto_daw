<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Create a new PermissionController instance.
     *
     * @return void
     */
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['index', 'indexAll', 'users']]);
        //$this->middleware('permission:permission-list|permission-create|permission-edit|permission-delete', ['except' => ['index', 'indexAll', 'users']]);
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
            return response()->json(['error' => $e->getMessage()]);
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
            return response()->json(['error' => $e->getMessage()]);
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
            return response()->json(['error' => $e->getMessage()]);
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
            //Revoke & add new permissions
            //Input example -> {"data":["product-list","config-list","config-edit"]}
            $user->syncPermissions(json_decode($request->permissions,true));
            
            return response()->json(['message' => 'Se han asignado los permisos correctamente al usuario']);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
