<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Create a new RoleController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => 'index']);
        $this->middleware('permission:role-list', ['only' => ['show', 'userRole']]);
        $this->middleware('permission:role-create', ['only' => ['create']]);
        $this->middleware('permission:role-edit', ['only' => ['update', 'userAssignRole']]);
        $this->middleware('permission:role-delete', ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource of roles.
     */
    public function index()
    {
        try{
            $roles = Role::all();
            return response()->json($roles);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {        
        try{
            $role = Role::find($id);
            return response()->json($role);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the user role.
     */
    public function userRole(string $id)
    {        
        try{
            $user = User::find($id);
            $role = $user->roles->first()->id;
            return response()->json($role);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Create the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try{
            $this->validate($request, [
                'name' => 'required',
            ]);
        
            $role = Role::create(['name' => $request->name]);
            //All role save by api guard name, I have only one api start endpoint
            
            return response()->json(['message' => 'Se ha creado el rol correctamente']);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $this->validate($request, [
                'name' => 'required',
            ]);
        
            $role = Role::find($id);
            $role->name = $request->name;
            $role->save();
            
            return response()->json(['message' => 'Se ha actualizado el rol correctamente']);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try{
            $role = Role::find($id);
            $role->delete();
            
            return response()->json(['message' => 'Se ha eliminado el rol correctamente']);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Assign the specified role to user in storage.
     *
     * @param  \Illuminate\Http\Request  $request (role id)
     * @param  int  $id (user id)
     * @return \Illuminate\Http\Response
     */
    public function userAssignRole(Request $request, $id)
    {
        try{
            $this->validate($request, [
                'id' => 'required',
            ]);
            $role = Role::find($request->id);
            $user = User::find($id);
            $user->roles()->detach();
            $user->assignRole([$role->id]);
            
            return response()->json(['message' => 'Se ha asignado el rol correctamente al usuario']);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
