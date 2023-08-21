<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptProduct;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\User;
use Faker\Factory as Faker;

class CreateGoodsReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        $users = User::all();
        $suppliers = Supplier::all();

        /*
        for ($i=0; $i < $faker->numberBetween($min = 1, $max = count($suppliers)-1); $i++) { 
            $goodReceipt = GoodsReceipt::create([
                'idsupplier' => $suppliers[$faker->numberBetween($min = 1, $max = count($suppliers)-1)]->id,
                'iduser' => 3,
                'date' => $faker->date,
                'time' => $faker->time,
                'docnum' => $faker->randomElement(['A', 'B']).$faker->numberBetween($min = 100000000, $max = 999999999),
            ]);
            $productsOfSupplier = Product::all()->where('supplier', $goodReceipt->idsupplier);
            if(count($productsOfSupplier) > 0){
                foreach ($productsOfSupplier as $key => $prod) {                
                    $goodReceiptProduct = GoodsReceiptProduct::create([
                        'idgoodsreceipt' => $goodReceipt->id,
                        'idproduct' => $prod->id,
                        'quantity' => $faker->numberBetween($min = 1, $max = 99),
                        'price' => $prod->price,
                    ]);
                };
            }else{
                $goodReceipt->delete();
            }
        }
        */

        for ($i=0; $i < 1000; $i++) { 
            $goodReceipt = GoodsReceipt::create([
                'idsupplier' => $suppliers[$faker->numberBetween($min = 1, $max = count($suppliers)-1)]->id,
                'iduser' => $faker->numberBetween($min = 1, $max = count($users)-1),
                'date' => $faker->date,
                'time' => $faker->time,
                'docnum' => $faker->randomElement(['A', 'B']).$faker->numberBetween($min = 100000000, $max = 999999999),
            ]);
            $productsOfSupplier = Product::all()->where('supplier', $goodReceipt->idsupplier);
            if(count($productsOfSupplier) > 0){
                foreach($productsOfSupplier as $prod){
                    $goodReceiptProduct = GoodsReceiptProduct::create([
                        'idgoodsreceipt' => $goodReceipt->id,
                        'idproduct' => $prod->id,
                        'quantity' => $faker->numberBetween($min = 1, $max = 99),
                        'price' =>$faker->randomFloat(2, $min = $prod->price - $prod->price * 0.25, $max = $prod->price + $prod->price * 0.25),
                    ]);
                }
            }else{
                $goodReceipt->delete();
            }
        }
    }
}
