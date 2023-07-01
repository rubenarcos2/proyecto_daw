<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Support\Jsonable;
use App;

class ProductController extends Controller
{
    /**
     * Create a new instance of AuthController and assign the permission for each function.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['index']]);
        $this->middleware('permission:product-list', ['only' => ['show']]);
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
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function show($id){
        try{
            $product = Product::find($id);
            if (empty($product))
                return response()->json(['error' => 'No existe un producto para el id dado']);
            else
                return response()->json($product);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }

    public function store(Request $request){
        try{
            $request->validate([
                'name' => 'required|min:3',
                'description' => 'required|min:3',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'price' => 'required',
                'stock' => 'required',
            ]);

            $product = new Product;
            $product->name = $request->name;
            $product->description = $request->description;
            if(!empty($request->image) || $request->image !== null){
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

            return response()->json(['message' => 'Se ha creado el producto correctamente']);
        }catch(\Exception $e){
            $file_path = public_path().'/storage/assets/img/products/'.$image_name;
            if(file_exists($file_path))
                if(!unlink($file_path))
                    throw new \Exception("No se ha podido actualizar la imagen");
            $thumbail_path = URL::to('') . '/storage/assets/img/products/' . substr($image_name,0,strpos($image_name, '.'));
            $file_path_ext = substr($image_name,strpos($image_name, '.'));
            if(file_exists($thumbail_path.'_32x32'.$file_path_ext))
                if(!unlink($thumbail_path.'_32x32'.$file_path_ext))
                    throw new \Exception("No se ha podido actualizar la imagen 32x32");
            if(file_exists($thumbail_path.'_128x128'.$file_path_ext))
                if(!unlink($thumbail_path.'_128x128'.$file_path_ext))
                    throw new \Exception("No se ha podido actualizar la imagen 128x128");
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }

    public function destroy(Request $request){
        try{
            $request->validate([
                'id' => 'required'
            ]);

            $product = Product::find($request->id);
            if($product->image !== URL::to('') . '/storage/assets/img/products/no-image.png'){
                $image_name = substr($product->image, strlen(URL::to('').'/storage/assets/img/products/'));
                $file_path = public_path().'/storage/assets/img/products/'.$image_name;
                if(file_exists($file_path))
                    if(!unlink($file_path))
                        throw new \Exception("No se ha podido actualizar la imagen");
                $thumbail_path = public_path().'/storage/assets/img/products/' . substr($image_name,0,strpos($image_name, '.'));
                $file_path_ext = substr($image_name,strpos($image_name, '.'));
                if(file_exists($thumbail_path.'_32x32'.$file_path_ext))
                    if(!unlink($thumbail_path.'_32x32'.$file_path_ext))
                        throw new \Exception("No se ha podido actualizar la imagen 32x32");
                if(file_exists($thumbail_path.'_128x128'.$file_path_ext))
                    if(!unlink($thumbail_path.'_128x128'.$file_path_ext))
                        throw new \Exception("No se ha podido actualizar la imagen 128x128");
            }
            $product->delete();
            return response()->json(['message' => 'Se ha eliminado el producto correctamente']);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request){
        try{
            $request->validate([
                'name' => 'required|min:3',
                'description' => 'required|min:3',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'price' => 'required',
                'stock' => 'required',
            ]);

            $product = Product::find($request->id);
            $product->name = $request->name;
            $product->description = $request->description;
            if($request->hasFile('image') && $request->file('image')->isValid()){
                if($product->image !== URL::to('') . '/storage/assets/img/products/no-image.png'){
                    $image_name = substr($product->image, strlen(URL::to('').'/storage/assets/img/products/'));
                    $file_path = public_path().'/storage/assets/img/products/'.$image_name;
                    if(file_exists($file_path))
                        if(!unlink($file_path))
                            throw new \Exception("No se ha podido actualizar la imagen");
                    $file_path = public_path().'/storage/assets/img/products/'. substr($image_name,0,strpos($image_name, '.'));
                    $thumbail_path = URL::to('') . '/storage/assets/img/products/' . substr($image_name,0,strpos($image_name, '.'));
                    $file_path_ext = substr($image_name,strpos($image_name, '.'));
                    if(file_exists($file_path.'_32x32'.$file_path_ext))
                        if(!unlink($file_path.'_32x32'.$file_path_ext))
                            throw new \Exception("No se ha podido actualizar la imagen 32x32");
                    if(file_exists($file_path.'_128x128'.$file_path_ext))
                        if(!unlink($file_path.'_128x128'.$file_path_ext))
                            throw new \Exception("No se ha podido actualizar la imagen 128x128");
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

            return response()->json(['message' => 'Se ha modificado el producto correctamente']);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    private function saveThumbnail($saveToDir, $imagePath, $imageName, $max_x, $max_y) {
        try{
            preg_match("'^(.*)\.(gif|jpe?g|png)$'i", $imageName, $ext);
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
                imagecopyresized($save, $im, 0, 0, 0, 0, imagesx($save), imagesy($save), $x, $y);
    
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
        }catch(\Exception $e){
            throw $e;
        }
    }
}
