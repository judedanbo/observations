<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class OfficeRolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Document Permissions
        Permission::create(['name' => 'view all offices']);
        Permission::create(['name' => 'view office']);
        Permission::create(['name' => 'create office']);
        Permission::create(['name' => 'update office']);
        Permission::create(['name' => 'delete office']);
        Permission::create(['name' => 'restore office']);
        Permission::create(['name' => 'destroy office']);

        Role::where('name', 'Super Administrator')
            ->first()
            ->givePermissionTo([
                'view all offices',
                'view office',
                'create office',
                'update office',
                'delete office',
                'restore office',
                'destroy office',
            ]);

        // Role::create(['name' => 'Super Administrator'])
        //     ->givePermissionTo(Permission::all());
    }
}
