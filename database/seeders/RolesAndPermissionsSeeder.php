<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Staff Permissions
        Permission::create(['name' => 'create staff']);
        Permission::create(['name' => 'edit staff']);
        Permission::create(['name' => 'delete staff']);
        Permission::create(['name' => 'restore staff']);
        Permission::create(['name' => 'destroy staff']);

        // Audit permissions
        Permission::create(['name' => 'create audit']);
        Permission::create(['name' => 'edit audit']);
        Permission::create(['name' => 'delete audit']);
        Permission::create(['name' => 'restore audit']);
        Permission::create(['name' => 'destroy audit']);
        Permission::create(['name' => 'publish audit']);
        Permission::create(['name' => 'unpublish audit']);
        Permission::create(['name' => 'assign audit']);
        Permission::create(['name' => 'unassign audit']);
        Permission::create(['name' => 'view audit']);
        Permission::create(['name' => 'view all audit']);
        Permission::create(['name' => 'view assigned audit']);
        Permission::create(['name' => 'view unassigned audit']);
        Permission::create(['name' => 'view audit report']);
        Permission::create(['name' => 'view audit report all']);

        // Team permissions
        Permission::create(['name' => 'create team']);
        Permission::create(['name' => 'edit team']);
        Permission::create(['name' => 'delete team']);
        Permission::create(['name' => 'restore team']);
        Permission::create(['name' => 'destroy team']);
        Permission::create(['name' => 'add staff']);
        Permission::create(['name' => 'remove staff']);

        // Observation Permission
        Permission::create(['name' => 'view all observations']);
        Permission::create(['name' => 'create observation']);
        Permission::create(['name' => 'edit observation']);
        Permission::create(['name' => 'delete observation']);
        Permission::create(['name' => 'restore observation']);
        Permission::create(['name' => 'destroy observation']);

        // finding permissions
        Permission::create(['name' => 'view all findings']);
        Permission::create(['name' => 'create finding']);
        Permission::create(['name' => 'edit finding']);
        Permission::create(['name' => 'delete finding']);
        Permission::create(['name' => 'restore finding']);
        Permission::create(['name' => 'destroy finding']);

        // action permissions
        Permission::create(['name' => 'view all actions']);
        Permission::create(['name' => 'create action']);
        Permission::create(['name' => 'edit action']);
        Permission::create(['name' => 'delete action']);
        Permission::create(['name' => 'restore action']);
        Permission::create(['name' => 'destroy action']);

        // Department permissions
        Permission::create(['name' => 'create department']);
        Permission::create(['name' => 'edit department']);
        Permission::create(['name' => 'delete department']);
        Permission::create(['name' => 'restore department']);
        Permission::create(['name' => 'destroy department']);

        // Unit permissions
        Permission::create(['name' => 'create unit']);
        Permission::create(['name' => 'edit unit']);
        Permission::create(['name' => 'delete unit']);
        Permission::create(['name' => 'restore unit']);
        Permission::create(['name' => 'destroy unit']);

        // 

        // create permissions
        Permission::create(['name' => 'edit articles']);
        Permission::create(['name' => 'delete articles']);
        Permission::create(['name' => 'publish articles']);
        Permission::create(['name' => 'unpublish articles']);

        // create roles and assign created permissions

        // this can be done as separate statements

        Role::create(['name' => 'super-administrator'])
            ->givePermissionTo(Permission::all());
        Role::create(['name' => 'system-administrator'])
            ->givePermissionTo([
                'restore staff',
                'destroy staff',
                'restore audit',
                'restore audit',
                'restore observation',
                'destroy observation',
            ]);
        Role::create(['name' => 'Staff'])
            ->givePermissionTo([]);
        Role::create(['name' => 'Audit Manager'])
            ->givePermissionTo([]);
        Role::create(['name' => 'Audit Reviewer'])
            ->givePermissionTo([]);
        Role::create(['name' => 'Team Leader'])
            ->givePermissionTo([]);
        Role::create(['name' => 'Team Member'])
            ->givePermissionTo([]);
        Role::create(['name' => 'Auditee'])
            ->givePermissionTo([]);
        Role::create(['name' => 'Quality Manager'])
            ->givePermissionTo([]);
        Role::create(['name' => 'Parliamentarian'])
            ->givePermissionTo([]);


        // $role->givePermissionTo('edit articles');
    }
}
