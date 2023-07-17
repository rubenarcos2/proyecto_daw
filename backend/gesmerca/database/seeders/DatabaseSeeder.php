<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            CreatePermissionTableSeeder::class,
            CreateAdminUserSeeder::class,
            CreateUserSeeder::class,
            CreateEmployeeSeeder::class,
            CreateConfig::class,
            CreateSuppliersSeeder::class,
            CreateProductsSeeder::class,            
            CreateGoodsReceiptSeeder::class,
        ]);
    }
}
