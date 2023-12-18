<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\URL;
use Faker\Factory as Faker;

class CreateProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $NUM_SUPPLIERS = 250;
        try{
            $faker = Faker::create('es-ES');
            $suppliers = Supplier::all();

            $this->removeImages();

            for ($i=0; $i<$NUM_SUPPLIERS; $i++) { 
                if (windows_os())
                    $URL_STORAGE = URL::to('').':8000/storage/assets/img/products/';
                else
                    $URL_STORAGE = URL::to('').'/storage/assets/img/products/';
                
                $imageFaker = $faker->image('storage/app/public/assets/img/products',640,480, 'products', false);
                if(empty($imageFaker) || $imageFaker === null){
                    $prod = Product::create([
                        'name' => $faker->words(3, true),
                        'description' => $faker->sentence(),
                        'supplier' => $suppliers[$faker->numberBetween($min = 1, $max = count($suppliers)-1)]->id,
                        'image' => $URL_STORAGE.'no-image.png',
                        'price' => $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 100),
                        'stock' => 0
                    ]);
                }else{
                    $prod = Product::create([
                        'name' => $faker->words(3, true),
                        'description' => $faker->sentence(),
                        'supplier' => $suppliers[$faker->numberBetween($min = 1, $max = count($suppliers)-1)]->id,
                        'image' => $URL_STORAGE.$imageFaker,
                        'price' => $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 100),
                        'stock' => 0
                    ]);
                    $image_name = substr($prod->image, strlen($URL_STORAGE));
                    if (windows_os()) {
                        $file_path = storage_path().'\\app\\public\\assets\\img\\products\\'.$image_name;
                        $thumbail_path = storage_path().'\\app\\public\\assets\\img\\products\\';
                    }else{
                        $file_path = storage_path().'/app/public/assets/img/products/'.$image_name;
                        $thumbail_path = storage_path().'/app/public/assets/img/products/';
                    }
                    $this->saveThumbnail($thumbail_path, $file_path, pathinfo($prod->image, PATHINFO_BASENAME), 32, 32);
                    $this->saveThumbnail($thumbail_path, $file_path, pathinfo($prod->image, PATHINFO_BASENAME), 128, 128);
                }
                $this->command->info('  Create product ' . strval($prod->id) . ' / '.$NUM_SUPPLIERS. ' ........ DONE');
            }

            if (!windows_os()) {
                chmod('/var/www/html/storage/', 0755);
                chmod('/var/www/html/public/', 0755);
            }
        }catch(\Exception $e){
            $this->command->error('ERROR: ' . $e);
            throw $e;
        }
    }

    private function removeImages(): void
    {
        try{
            if (windows_os())
                $file_path = storage_path().'\\app\\public\\assets\\img\\products\\';
            else
                $file_path = storage_path().'/app/public/assets/img/products/';
            $files = glob($file_path . '/*');

            foreach ($files as $file) {
                if(!str_contains($file, 'no-image'))
                    unlink($file);
            }
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
}
