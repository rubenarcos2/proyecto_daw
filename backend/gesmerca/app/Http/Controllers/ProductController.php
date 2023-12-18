<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\GoodsReceiptProduct;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;
use App;

class ProductController extends Controller
{
    /**
     * Create a new instance of AuthController and assign the permission for each function.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['index', 'all']]);
        $this->middleware('permission:product-list', ['only' => ['show', 'allBySupplier']]);
        $this->middleware('permission:product-create', ['only' => ['store']]);
        $this->middleware('permission:product-edit', ['only' => ['update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        try{
            App::setLocale('es');
            session()->put('locale', 'es');  
            $products = Product::orderBy('updated_at', 'desc')->paginate(8);
            foreach ($products as $prod) {
                $prod->supplierName = Supplier::find($prod->supplier)->name;

                $image_name = substr($prod->image, strlen(URL::to('').'/storage/assets/img/products/'));
                $thumbail_path = URL::to('') . '/storage/assets/img/products/' . substr($image_name,0,strpos($image_name, '.'));
                $file_path = public_path() . '/storage/assets/img/products/' . substr($image_name,0,strpos($image_name, '.'));
                $file_path_ext = substr($image_name,strpos($image_name, '.'));

                if(str_contains($image_name, 'no-image')){
                    if(file_exists($file_path.'_32x32.png'))
                        $prod->thumbail_32x32 = $thumbail_path.'_32x32.png';
                    if(file_exists($file_path.'_128x128.png'))
                        $prod->thumbail_128x128 = $thumbail_path.'_128x128.png';
                }else{
                    file_exists($file_path.'_32x32'.$file_path_ext) ? $prod->thumbail_32x32 = $thumbail_path.'_32x32'.$file_path_ext : $prod->thumbail_32x32 = URL::to('') . '/storage/assets/img/products/no-image_32x32.png';
                    file_exists($file_path.'_128x128'.$file_path_ext) ? $prod->thumbail_128x128 = $thumbail_path.'_128x128'.$file_path_ext : $prod->thumbail_128x128 = URL::to('') . '/storage/assets/img/products/no-image_128x128.png'; 
                }
            }
            
            return response()->json($products);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the all products.
     */
    public function all()
    {
        try{
            $products = Product::all();
            foreach ($products as $prod) {
                $prod->supplierName = Supplier::find($prod->supplier)->name;

                $image_name = substr($prod->image, strlen(URL::to('').'/storage/assets/img/products/'));
                $thumbail_path = URL::to('') . '/storage/assets/img/products/' . substr($image_name,0,strpos($image_name, '.'));
                $file_path = public_path() . '/storage/assets/img/products/' . substr($image_name,0,strpos($image_name, '.'));
                $file_path_ext = substr($image_name,strpos($image_name, '.'));

                if(str_contains($image_name, 'no-image')){
                    if(file_exists($file_path.'_32x32.png'))
                        $prod->thumbail_32x32 = $thumbail_path.'_32x32.png';
                    if(file_exists($file_path.'_128x128.png'))
                        $prod->thumbail_128x128 = $thumbail_path.'_128x128.png';
                }else{
                    file_exists($file_path.'_32x32'.$file_path_ext) ? $prod->thumbail_32x32 = $thumbail_path.'_32x32'.$file_path_ext : $prod->thumbail_32x32 = URL::to('') . '/storage/assets/img/products/no-image_32x32.png';
                    file_exists($file_path.'_128x128'.$file_path_ext) ? $prod->thumbail_128x128 = $thumbail_path.'_128x128'.$file_path_ext : $prod->thumbail_128x128 = URL::to('') . '/storage/assets/img/products/no-image_128x128.png'; 
                }
            }
            return response()->json($products);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the all products of a supplier.
     */
    public function allBySupplier($idSupplier)
    {
        try{
            App::setLocale('es');
            session()->put('locale', 'es');  
            $products = Product::orderBy('updated_at', 'desc')->where('supplier', $idSupplier)->paginate(4);
            foreach ($products as $prod) {
                $prod->supplierName = Supplier::find($prod->supplier)->name;

                $image_name = substr($prod->image, strlen(URL::to('').'/storage/assets/img/products/'));
                $thumbail_path = URL::to('') . '/storage/assets/img/products/' . substr($image_name,0,strpos($image_name, '.'));
                $file_path = public_path() . '/storage/assets/img/products/' . substr($image_name,0,strpos($image_name, '.'));
                $file_path_ext = substr($image_name,strpos($image_name, '.'));

                if(str_contains($image_name, 'no-image')){
                    if(file_exists($file_path.'_32x32.png'))
                        $prod->thumbail_32x32 = $thumbail_path.'_32x32.png';
                    if(file_exists($file_path.'_128x128.png'))
                        $prod->thumbail_128x128 = $thumbail_path.'_128x128.png';
                }else{
                    file_exists($file_path.'_32x32'.$file_path_ext) ? $prod->thumbail_32x32 = $thumbail_path.'_32x32'.$file_path_ext : $prod->thumbail_32x32 = URL::to('') . '/storage/assets/img/products/no-image_32x32.png';
                    file_exists($file_path.'_128x128'.$file_path_ext) ? $prod->thumbail_128x128 = $thumbail_path.'_128x128'.$file_path_ext : $prod->thumbail_128x128 = URL::to('') . '/storage/assets/img/products/no-image_128x128.png'; 
                }
            }
            
            return response()->json($products);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id){
        try{
            $product = Product::find($id);
            if ($product == null)
                return response()->json(['error' => 'No existe un producto para el id dado'], 400);
            else{
                $product->supplierName = Supplier::find($product->supplier)->name;

                $product->price = floatval($product->price);
                $product->priceMin = $this->getPriceMin($id);
                $product->priceMax = $this->getPriceMax($id);
                $product->priceAvg = $this->getPriceAvg($id);
                return response()->json($product);
            }
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request){
        try{
            $request->validate([
                'name' => 'required|min:3',
                'description' => 'required|min:3',
                'supplier' => 'required',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'price' => 'required',
                'stock' => 'required',
            ]);
            $name = Product::where('name', $request->name)->where('supplier', $request->supplier)->first();
            if(is_null($name)) {
                $product = new Product;
                $product->name = $request->name;
                $product->description = $request->description;
                $product->supplier = $request->supplier;
                if($request->hasFile('image') && $request->file('image')->isValid()){
                    $image_path = $request->file('image')->store('assets/img/products', 'public');
                    $product->image = URL::to('') . '/storage/' . $image_path;
                    sleep(2);
                    $image_name = substr($product->image, strlen(URL::to('').'/storage/assets/img/products/'));
                    $file_path = public_path().'/storage/assets/img/products/'.$image_name;
                    $thumbail_path = public_path().'/storage/assets/img/products/';
                    $this->saveThumbnail($thumbail_path, $file_path, pathinfo($product->image, PATHINFO_BASENAME), 32, 32);
                    $this->saveThumbnail($thumbail_path, $file_path, pathinfo($product->image, PATHINFO_BASENAME), 128, 128);
                }else{
                    $product->image = URL::to('') . '/storage/assets/img/products/no-image.png';
                }
                $product->price = $request->price;
                $product->stock = $request->stock;
                $product->save();
                return response()->json(['message' => 'Se ha creado el producto']);
            }else
                return response()->json(['error' => 'No se ha creado el producto: ya existe un producto con el nombre dado'], 400);
        }catch(\Exception $e){
            if(isset($image_name)){
                $this->deleteImages($product->image);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request){
        try{
            $request->validate([
                'id' => 'required'
            ]);

            $product = Product::find($request->id);
            if($product->image !== URL::to('') . '/storage/assets/img/products/no-image.png'){
                $this->deleteImages($product->image);
            }
            $product->delete();
            return response()->json(['message' => 'Se ha eliminado el producto']);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request){
        try{
            $request->validate([
                'name' => 'required|min:3',
                'description' => 'required|min:3',
                'supplier' => 'required',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'price' => 'required',
                'stock' => 'required',
            ]);

            $name = Product::where('name', $request->name)->where('supplier', $request->supplier)->where('id', '!=', $request->id)->first();
            if(is_null($name)) {
                $product = Product::find($request->id);
                $product->name = $request->name;
                $product->description = $request->description;
                $product->supplier = $request->supplier;
                if($request->hasFile('image') && $request->file('image')->isValid()){
                    if($product->image !== URL::to('') . '/storage/assets/img/products/no-image.png'){
                        $this->deleteImages($product->image);
                    }
                    $image_path = $request->file('image')->store('assets/img/products', 'public');
                    $product->image = URL::to('') . '/storage/' . $image_path;
                    sleep(2);
                    $image_name = substr($product->image, strlen(URL::to('').'/storage/assets/img/products/'));
                    $file_path = public_path().'/storage/assets/img/products/'.$image_name;
                    $thumbail_path = public_path().'/storage/assets/img/products/';
                    $this->saveThumbnail($thumbail_path, $file_path, pathinfo($product->image, PATHINFO_BASENAME), 32, 32);
                    $this->saveThumbnail($thumbail_path, $file_path, pathinfo($product->image, PATHINFO_BASENAME), 128, 128);
                }
                $product->price = $request->price;
                $product->stock = $request->stock;
                $product->save();

                return response()->json(['message' => 'Se ha modificado el producto']);
            }else
                return response()->json(['error' => 'No se ha modificado el producto: ya existe un producto con el nombre dado'], 400);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getPriceMin($idproduct){
        try{
            $grp = GoodsReceiptProduct::where('idproduct', $idproduct)->select('price')->orderBy('price', 'asc')->first();
            if(empty($grp))
                return 0;
            else
                return $grp->price;
        }catch(\Exception $e){
            throw $e;
        }
    }
    
    private function getPriceMax($idproduct){
        try{
            $grp = GoodsReceiptProduct::where('idproduct', $idproduct)->select('price')->orderBy('price', 'desc')->first();
            if(empty($grp))
                return 0;
            else
                return $grp->price;
        }catch(\Exception $e){
            throw $e;
        }
    }
    
    private function getPriceAvg($idproduct){
        try{
            $grp = GoodsReceiptProduct::where('idproduct', $idproduct);
            if(empty($grp))
                return 0;
            else
                return round($grp->avg('price'),2);
        }catch(\Exception $e){
            throw $e;
        }
    }

    private function saveThumbnail($saveToDir, $imagePath, $imageName, $max_x, $max_y) {
        try{
            if(preg_match("'^(.*)\.(gif|jpe?g|png)$'i", $imageName, $ext)){
                switch (strtolower($ext[2])) {
                    case 'jpg' : 
                    case 'jpeg': $im   = imagecreatefromjpeg($imagePath);
                                break;
                    case 'gif' : $im   = imagecreatefromgif($imagePath);
                                break;
                    case 'png' : $im   = @imagecreatefrompng($imagePath);
                                break;
                    default    : $stop = true;
                                break;
                }
                
                if (!isset($stop)) {
                    $x = imagesx($im);
                    $y = imagesy($im);
                
                    if (($max_x/$max_y) < ($x/$y)) {
                        $save = imagecreatetruecolor($x/($x/$max_x), $y/($x/$max_x));
                    }
                    else {
                        $save = imagecreatetruecolor($x/($y/$max_y), $y/($y/$max_y));
                    }
                    //imagecopyresized($save, $im, 0, 0, 0, 0, imagesx($save), imagesy($save), $x, $y);
                    imagecopyresampled($save, $im, 0, 0, 0, 0, imagesx($save), imagesy($save), $x, $y);
                    
                    switch (strtolower($ext[2])) {
                        case 'jpg' : 
                        case 'jpeg': imagejpeg($save, "{$saveToDir}{$ext[1]}_{$max_x}x{$max_y}.jpg");
                                    break;
                        case 'gif' : imagegif($save, "{$saveToDir}{$ext[1]}_{$max_x}x{$max_y}.gif");
                                    break;
                        case 'png' : 
                            $black = imagecolorallocate($im, 0, 0, 0);
                            imagecolortransparent($save, $black);
                            @imagepng($save, "{$saveToDir}{$ext[1]}_{$max_x}x{$max_y}.png");
                                    break;
                    }
        
                    imagedestroy($im);
                    imagedestroy($save);
                } 
            }
        }catch(\Exception $e){
            throw $e;
        }
    }

    private function deleteImages($requestImage){
        try{
            $image_name = substr($requestImage, strlen(URL::to('').'/storage/assets/img/products/'));
            if(!empty($image_name) || $image_name == null){
                $file_path = public_path().'/storage/assets/img/products/'.$image_name;
                if(file_exists($file_path))
                    if(!unlink($file_path))
                        throw new \Exception("No se ha podido actualizar la imagen");
                $file_path = public_path().'/storage/assets/img/products/'. substr($image_name,0,strpos($image_name, '.'));
                $thumbail_path = public_path() . '/storage/assets/img/products/' . substr($image_name,0,strpos($image_name, '.'));
                $file_path_ext = substr($image_name,strpos($image_name, '.'));
                if(file_exists($file_path.'_32x32'.$file_path_ext))
                    if(!unlink($file_path.'_32x32'.$file_path_ext))
                        throw new \Exception("No se ha podido actualizar la imagen 32x32");
                if(file_exists($file_path.'_128x128'.$file_path_ext))
                    if(!unlink($file_path.'_128x128'.$file_path_ext))
                        throw new \Exception("No se ha podido actualizar la imagen 128x128");
            }
        }catch(\Exception $e){
            throw $e;
        }
    }
}
