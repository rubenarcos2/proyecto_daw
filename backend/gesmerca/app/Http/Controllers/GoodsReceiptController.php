<?php

namespace App\Http\Controllers;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptProduct;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App;

class GoodsReceiptController extends Controller
{
    /**
     * Create a new GoodsReceiptController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['index']]);
        $this->middleware('permission:goodsreceipt-list', ['only' => ['show', 'products']]);
        $this->middleware('permission:goodsreceipt-create', ['only' => ['create']]);
        $this->middleware('permission:goodsreceipt-edit', ['only' => ['update', 'addProduct', 'deleteProduct']]);
        $this->middleware('permission:goodsreceipt-delete', ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource of goods receipt.
     */
    public function index()
    {
        try{
            App::setLocale('es');
            session()->put('locale', 'es');  
            $goodReceipts = GoodsReceipt::orderBy('date', 'desc')->paginate(8);
            foreach ($goodReceipts as $goodReceipt) {
                $supplier = Supplier::find($goodReceipt->idsupplier);
                $user = User::find($goodReceipt->iduser);
                $goodReceipt->supplierName = $supplier->name;
                $goodReceipt->userName = $user->name;
            }
            return response()->json($goodReceipts);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource of goods receipt.
     */
    public function show(string $id)
    {        
        try{
            $goodReceipt = GoodsReceipt::find($id);
            $supplier = Supplier::find($goodReceipt->idsupplier);
            $user = User::find($goodReceipt->iduser);
            $goodReceipt->supplierName = $supplier->name;
            $goodReceipt->userName = $user->name;
            return response()->json($goodReceipt);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource of goods receipt.
     */
    public function products(string $id)
    {        
        try{
            $goodReceipt = GoodsReceipt::find($id);
            foreach ($goodReceipt->products as $product) {
                $supplierProduct = Product::find($product->idproduct);
                $product->nameproduct = $supplierProduct->name;
            }
            return response()->json($goodReceipt->products);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the all Good Receipts.
     */
    public function all()
    {
        try{
            $goodReceipts = GoodsReceipt::all();
            foreach ($goodReceipts as $goodReceipt) {
                $supplier = Supplier::find($goodReceipt->idsupplier);
                $user = User::find($goodReceipt->iduser);
                $goodReceipt->supplierName = $supplier->name;
                $goodReceipt->userName = $user->name;
            }
            return response()->json($goodReceipts);
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
    public function addProduct(string $id, Request $request)
    {
        try{
            $this->validate($request, [
                'idgoodsreceipt' => 'required',
                'idproduct' => 'required',
                'quantity' => 'required',
                'price' => 'required'
            ]);
        
            $supplierGoodReceipt = GoodsReceipt::find($id)->idsupplier;
            $supplierProduct = Product::find($request->idproduct)->supplier;

            if($supplierGoodReceipt === $supplierProduct){
                $goodReceiptProduct = GoodsReceiptProduct::create([
                    'idgoodsreceipt' => $request->idgoodsreceipt,
                    'idproduct' => $request->idproduct,
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                ]);

                //Increment product stock
                $product = Product::find($request->idproduct);
                $product->stock += $request->quantity;
                $product->save();
    
                return response()->json(['message' => 'Se ha añadido el producto al albarán de recepción de mercancía correctamente']);
            }else
                return response()->json(['message' => 'El producto no pertenece al mismo proveedor que albarán de recepción de mercancía']);
            
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
    public function deleteProduct(string $id, Request $request)
    {
        try{
            $this->validate($request, [
                'idgoodsreceiptproduct' => 'required',
                'quantity' => 'required',
            ]);
        
            $goodReceiptProduct = GoodsReceiptProduct::find($request->idgoodsreceiptproduct);
            $goodReceiptProduct->delete();

            //Decrement product stock
            $product = Product::find($goodReceiptProduct->idproduct);
            $product->stock -= $request->quantity;
            $product->save();

            return response()->json(['message' => 'Se ha eliminado el producto del albarán de recepción de mercancía correctamente']);
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
                'idsupplier' => 'required',
                'iduser' => 'required',
                'date' => 'required',
                'time' => 'required',
                'docnum' => 'required',
            ]);
        
            $goodReceipt = GoodsReceipt::create([
                'idsupplier' => $request->idsupplier,
                'iduser' => $request->iduser,
                'date' => $request->date,
                'time' => $request->time,
                'docnum' => $request->docnum,
            ]);

            return response()->json(['message' => 'Se ha creado el albarán de recepción de mercancía correctamente', 'id' => $goodReceipt->id]);
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
                'idsupplier' => 'required',
                'iduser' => 'required',
                'date' => 'required',
                'time' => 'required',
                'docnum' => 'required',
            ]);
        
            $goodReceipt = GoodsReceipt::find($id);
            $goodReceipt->idsupplier = $request->idsupplier;
            $goodReceipt->iduser = $request->iduser;
            $goodReceipt->date = $request->date;
            $goodReceipt->time = $request->time;
            $goodReceipt->docnum = $request->docnum;
            $goodReceipt->save();
            
            return response()->json(['message' => 'Se ha actualizado el albarán de recepción de mercancía correctamente correctamente']);
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
            $goodReceipt = GoodsReceipt::find($id);
            //Delete all products' line of Goods Receipt
            foreach ($goodReceipt->products as $prod) {
                $goodReceiptProduct = GoodsReceiptProduct::find($prod->id);
                $goodReceiptProduct->delete();
                //Decrement product stock
                $product = Product::find($prod->idproduct);
                $product->stock -= $prod->quantity;
                $product->save();
            }
            $goodReceipt->delete();
            
            return response()->json(['message' => 'Se ha eliminado el albarán de recepción de mercancía correctamente']);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Connect to external API wich content an AI process to estimate the next purchase price
     * 
     * Only enable on production server!!!
     */
    public function getPriceEst(Request $request){
        $response = Http::post('https://vps.rarcos.com:10450', [
            "idproduct" => intval($request->idproduct),
            "quantity" => intval($request->quantity)
        ]);
        return $response;
    }
}
