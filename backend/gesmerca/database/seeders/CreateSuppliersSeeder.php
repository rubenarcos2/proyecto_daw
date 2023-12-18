<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;
use Faker\Factory as Faker;

class CreateSuppliersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $NUM_GOODSRECEIPT = 15;

        $faker = Faker::create('es-ES');
        
        for ($i=0; $i<$NUM_GOODSRECEIPT; $i++) { 
            $supplier = Supplier::create([
                'cif_nif' => $faker->randomElement(['A', 'B']).$faker->numberBetween($min = 100000000, $max = 999999999), 
                'name' => $faker->name.$faker->randomElement([', S.L.',', S.A.', ', S.L.U', ', S.A.L.', ', SLL.', ', S.Coop.', ', S.Com.',', S.C.']),
                'address' => $faker->address,
                'city' => $faker->city,
                'phone' => $faker->numberBetween($min = 600000000, $max = 999999999),
                'email' => $faker->email,
                'web' => $faker->domainName,
                'notes' => $faker->text(),
            ]);
            $this->command->info('  Create supplier ' . strval($supplier->id) . ' / '.$NUM_GOODSRECEIPT.' ........ DONE');
        }
    }
}
