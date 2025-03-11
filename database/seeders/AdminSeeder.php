<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    $user = User::create([
        	'username' => 'Admin',
        	'email' => 'admin@gmail.com',
        	'password' =>Hash::make('12345678'),
            'sex' => 'Male',
        ]);



        $roleAdmin = Role::create(['name' => 'admin','guard_name' => 'web']);
        $roleUser = Role::create(['name' => 'user','guard_name' => 'web']);
        $roleManager = Role::create(['name' => 'manager','guard_name' => 'web']);
        $permissions = Permission::pluck('id','id')->all();
        $roleAdmin->syncPermissions($permissions);
        $user->assignRole([$roleAdmin->id]);
        $managerPermissions = [
            'product-list', 'product-create', 'product-edit',
            'category-list', 'category-create', 'category-edit',
            'order-list', 'order-create', 'order-edit',
            'color-list', 'color-create', 'color-edit',
            'size-list', 'size-create', 'size-edit',
            'brand-list', 'brand-create', 'brand-edit',
            'banner-list', 'banner-create', 'banner-edit',
        ];
        $roleManager->syncPermissions($managerPermissions);
        // $userPermissions = [
        //     'order-list', 'order-create', 'order-edit',
        // ];
        // $roleUser->syncPermissions($userPermissions);
    }
}
