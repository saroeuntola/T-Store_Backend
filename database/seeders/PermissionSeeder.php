<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $permissions = [

            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',

            'product-list',
            'product-create',
            'product-edit',
            'product-delete',

            'category-list',
            'category-create',
            'category-edit',
            'category-delete',

            'order-list',
            'order-create',
            'order-edit',
            'order-delete',

            'color-list',
            'color-create',
            'color-edit',
            'color-delete',

            'size-list',
            'size-create',
            'size-edit',
            'size-delete',

            'brand-list',
            'brand-create',
            'brand-edit',
            'brand-delete',

            'banner-list',
            'banner-create',
            'banner-edit',
            'banner-delete',

            'customer-list',
            'customer-create',
            'customer-edit',
            'customer-delete',
         ];
 foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}
