<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Usuario', 
            'email' => 'user@user.com',
            'password' => bcrypt('usuario')
        ]);
    
        $role = Role::create(['name' => 'User']);
     
        $user->syncPermissions(['product-list', 'config-list', 'config-edit']);
     
        $user->assignRole([$role->id]);
    }
}
