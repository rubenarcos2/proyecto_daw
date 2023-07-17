<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class CreatePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
           'role-list',
           'role-create',
           'role-edit',
           'role-delete',

           'product-list',
           'product-create',
           'product-edit',
           'product-delete',
           
           'config-list',
           'config-create',
           'config-edit',
           'config-delete',

           'permission-list',
           'permission-create',
           'permission-edit',
           'permission-delete',

           'supplier-list',
           'supplier-create',
           'supplier-edit',
           'supplier-delete',
        ];
     
        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}
