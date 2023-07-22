<?php

namespace App\Http\Controllers;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App;

class SupplierController extends Controller
{
    /**
     * Create a new PermissionController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['index', 'all']]);
        $this->middleware('permission:supplier-list', ['only' => ['show']]);
        $this->middleware('permission:supplier-create', ['only' => ['create']]);
        $this->middleware('permission:supplier-edit', ['only' => ['update']]);
        $this->middleware('permission:supplier-delete', ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource of suppliers.
     */
    public function index()
    {
        try{
            App::setLocale('es');
            session()->put('locale', 'es');  
            $suppliers = Supplier::orderBy('updated_at', 'desc')->paginate(8);
            return response()->json($suppliers);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the all suppliers.
     */
    public function all()
    {
        try{
            $suppliers = Supplier::all();
            return response()->json($suppliers);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource of supplier.
     */
    public function show(string $id)
    {        
        try{
            $supplier = Supplier::find($id);
            return response()->json($supplier);
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
                'cif_nif' => 'required',
                'name' => 'required',
                'address' => 'required',
                'city' => 'required',
                'phone' => 'required',
                'email' => 'nullable',
                'web' => 'nullable',
                'notes' => 'nullable',
            ]);
        
            $supplier = Supplier::create([
                'cif_nif' => $request->cif_nif,
                'name' => $request->name,
                'address' => $request->address,
                'city' => $request->city,
                'phone' => $request->phone,
                'email' => $request->email,
                'web' => $request->web,
                'notes' => $request->notes,
            ]);

            return response()->json(['message' => 'Se ha creado el proveedor correctamente']);
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
                'cif_nif' => 'required',
                'name' => 'required',
                'address' => 'required',
                'city' => 'required',
                'phone' => 'required',
                'email' => 'nullable',
                'web' => 'nullable',
                'notes' => 'nullable',
            ]);
        
            $supplier = Supplier::find($id);
            $supplier->cif_nif = $request->cif_nif;
            $supplier->name = $request->name;
            $supplier->address = $request->address;
            $supplier->city = $request->city;
            $supplier->phone = $request->phone;
            $supplier->email = $request->email;
            $supplier->web = $request->web;
            $supplier->notes = $request->notes;
            $supplier->save();
            
            return response()->json(['message' => 'Se ha actualizado el proveedor correctamente']);
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
            $supplier = Supplier::find($id);
            $supplier->delete();
            
            return response()->json(['message' => 'Se ha eliminado el proveedor correctamente']);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
