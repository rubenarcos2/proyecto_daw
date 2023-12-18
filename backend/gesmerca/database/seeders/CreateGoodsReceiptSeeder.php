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
        $NUM_GOODSRECEIPT = 50;

        $faker = Faker::create('es-ES');
        
        $users = User::all();
        $suppliers = Supplier::all();

        for ($i=0; $i < $NUM_GOODSRECEIPT; $i++) { 
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
                    //Increment product stock
                    $product = Product::find($prod->id);
                    $product->stock += $goodReceiptProduct->quantity;
                    $product->save();
                }
            }else{
                $goodReceipt->delete();
            }
            $this->command->info('  Create good receipt ' . strval($goodReceipt->id) . ' / ' .$NUM_GOODSRECEIPT .' ........ DONE');
        }
    }
}
