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
        $faker = Faker::create();
        
        for ($i=0; $i<15; $i++) { 
            $user = Supplier::create([
                'cif_nif' => $faker->randomElement(['A', 'B']).$faker->numberBetween($min = 100000000, $max = 999999999), 
                'name' => 'Proveedor '.$i+1,
                'address' => $faker->address,
                'city' => $faker->city,
                'phone' => $faker->numberBetween($min = 600000000, $max = 999999999),
                'email' => $faker->email,
                'web' => $faker->domainName,
                'notes' => $faker->text(),
            ]);
        }
    }
}
