<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;
use App\Models\User;
use Illuminate\Contracts\Support\Jsonable;
use App;


class ConfigController extends Controller
{
    /**
     * Create a new ConfigController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['indexAll', 'users']]);
        $this->middleware('permission:config-list|config-create|config-edit|config-delete', ['except' => ['indexAll', 'users']]);
    }

    /**
     * Display a listing of the resource of general configuration.
     */
    public function indexAll()
    {
        try{
            $configs = Config::all();
            return response()->json($configs);
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
            $configs = Config::find($id);
            return response()->json($configs);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function users(string $id)
    {
        try{
            $user = User::find($id);
            $configs = $user->configs;
            return response()->json($configs);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function configs(string $id)
    {
        try{
            $conf = Config::find($id);
            $users = $conf->users;
            return response()->json($users);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified configuration by user.
     */
    public function updateUserConfig(Request $request, string $id)
    {
        try{
            $request->validate([
                'name' => 'required|min:3',
                'value' => 'required|min:3',
                'description' => 'required|min:3',
            ]);

            $user = User::find($id);
            $conf = Config::where('name', $request->name)->firstOrFail();
            if($user !== null && $conf !== null){
                $user->configs()->detach($conf->id);
                $user->configs()->attach($conf->id, ['value' => $request->value, 'description' => $request->description]);
            
                return response()->json(['message' => 'Se ha actualizado la configuración del usuario correctamente']);
            }else
                throw new \Exception("ID del usuario o nombre de la configuración inválidos");
                
            
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Delete the specified configuration by user.
     */
    public function deleteUserConfig(Request $request, string $id)
    {
        try{
            $request->validate([
                'name' => 'required|min:3',
            ]);

            $user = User::find($id);
            $conf = Config::where('name', $request->name)->firstOrFail();
            if($user !== null && $conf !== null){
                $user->configs()->detach($conf->id);            
                return response()->json(['message' => 'Se ha eliminado la configuración del usuario correctamente']);
            }else
                throw new \Exception("ID del usuario o nombre de la configuración inválidos");
                
            
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update the general configuration.
     */
    public function update(Request $request){
        try{
            $request->validate([
                'name' => 'required|min:3',                
                'title' => 'required|min:3',
                'description' => 'required|min:3',
                'domain' => 'required|min:3',
                'value' => 'required|min:3',
            ]);

            $conf = Config::where('name', $request->name)->firstOrFail();            
            $conf->title = $request->title;
            $conf->description = $request->description;
            $conf->domain = $request->domain;
            $conf->value = $request->value;
            //$conf->configs()->detach();
            $conf->save();            

            return response()->json(['message' => 'Se ha modificado la configuración correctamente']);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
