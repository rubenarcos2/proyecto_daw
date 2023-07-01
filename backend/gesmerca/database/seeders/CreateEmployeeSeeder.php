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
     
        $user->syncPermissions(['product-list', 'product-create', 'product-edit', 'product-delete', 'config-list', 'config-edit']);
     
        $user->assignRole([$role->id]);
    }
}
