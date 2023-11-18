<?php

namespace Tests\Unit;
use Tests\TestCase;

use App;
use App\Models\Product;
use App\Http\Controllers\ProductController;
use App\Models\GoodsReceiptProduct;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class ProductTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_product_index(): void
    {
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

        $prod = new ProductController();
        $this->assertEquals(response()->json($products), $prod->index());
    }

    /**
     * A basic unit test example.
     */
    public function test_product_all(): void
    {
        $products = Product::all();
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

        $prod = new ProductController();
        $this->assertEquals(response()->json($products), $prod->all());
    }

    /**
     * A basic unit test example.
     */
    public function test_product_show(): void
    {
        $product = Product::find(1);
        $product->priceMin = $this->getPriceMin(1);
        $product->priceMax = $this->getPriceMax(1);
        $product->priceAvg = $this->getPriceAvg(1);

        $prod = new ProductController();
        $this->assertEquals(response()->json($product), $prod->show(1));
    }

    private function getPriceMin($idproduct){
        return GoodsReceiptProduct::where('idproduct', $idproduct)->select('price')->orderBy('price', 'asc')->first()->price;        
    }
    
    private function getPriceMax($idproduct){
        return GoodsReceiptProduct::where('idproduct', $idproduct)->select('price')->orderBy('price', 'desc')->first()->price;        
    }
    
    private function getPriceAvg($idproduct){
        $grp = GoodsReceiptProduct::where('idproduct', $idproduct);        
        return round($grp->avg('price'),2);
    }
}
