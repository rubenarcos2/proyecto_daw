<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Empleado', 
            'email' => 'employee@employee.com',
            'password' => bcrypt('empleado')
        ]);
    
        $role = Role::create(['name' => 'Employee']);
     
        $user->syncPermissions(['product-list', 'product-create', 'product-edit', 
                                'config-list', 'config-edit', 
                                'supplier-list', 'supplier-create', 'supplier-edit',
                                'goodsreceipt-list', 'goodsreceipt-create', 'goodsreceipt-edit', 'goodsreceipt-delete']);
     
        $user->assignRole([$role->id]);
    }
}
