<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GoodsReceipt;
use App\Models\Supplier;
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

        for ($i=0; $i < $faker->numberBetween($min = 1, $max = count($suppliers)-1); $i++) { 
            $goodReceipt = GoodsReceipt::create([
                'idsupplier' => $suppliers[$faker->numberBetween($min = 1, $max = count($suppliers)-1)]->id,
                'iduser' => 3,
                'date' => $faker->date,
                'time' => $faker->time,
                'docnum' => $faker->randomElement(['A', 'B']).$faker->numberBetween($min = 100000000, $max = 999999999),
            ]);           
        }
    }
}
