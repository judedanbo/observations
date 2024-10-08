<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Surcharge;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Document Permissions
        Permission::create(['name' => 'view all documents']);
        Permission::create(['name' => 'view document']);
        Permission::create(['name' => 'create document']);
        Permission::create(['name' => 'update document']);
        Permission::create(['name' => 'delete document']);
        Permission::create(['name' => 'restore document']);
        Permission::create(['name' => 'destroy document']);

        //Audit Status Permissions
        Permission::create(['name' => 'view all statuses']);
        Permission::create(['name' => 'view status']);
        Permission::create(['name' => 'create status']);
        Permission::create(['name' => 'update status']);
        Permission::create(['name' => 'delete status']);
        Permission::create(['name' => 'restore status']);
        Permission::create(['name' => 'destroy status']);

        //  institution permissions
        Permission::create(['name' => 'view all institutions']);
        Permission::create(['name' => 'view institution']);
        Permission::create(['name' => 'create institution']);
        Permission::create(['name' => 'update institution']);
        Permission::create(['name' => 'delete institution']);
        Permission::create(['name' => 'restore institution']);
        Permission::create(['name' => 'destroy institution']);

        // Leader Permissions
        Permission::create(['name' => 'view all leaders']);
        Permission::create(['name' => 'view leader']);
        Permission::create(['name' => 'create leader']);
        Permission::create(['name' => 'update leader']);
        Permission::create(['name' => 'delete leader']);
        Permission::create(['name' => 'restore leader']);
        Permission::create(['name' => 'destroy leader']);

        // Staff Permissions
        Permission::create(['name' => 'view all staff']);
        Permission::create(['name' => 'view staff']);
        Permission::create(['name' => 'create staff']);
        Permission::create(['name' => 'update staff']);
        Permission::create(['name' => 'delete staff']);
        Permission::create(['name' => 'restore staff']);
        Permission::create(['name' => 'destroy staff']);

        // Audit permissions
        Permission::create(['name' => 'view all audits']);
        Permission::create(['name' => 'view audit']);
        Permission::create(['name' => 'create audit']);
        Permission::create(['name' => 'update audit']);
        Permission::create(['name' => 'delete audit']);
        Permission::create(['name' => 'restore audit']);
        Permission::create(['name' => 'destroy audit']);
        Permission::create(['name' => 'publish audit']);
        Permission::create(['name' => 'unpublish audit']);
        Permission::create(['name' => 'assign audit']);
        Permission::create(['name' => 'unassign audit']);
        Permission::create(['name' => 'view assigned audit']);
        Permission::create(['name' => 'view unassigned audit']);
        Permission::create(['name' => 'view audit report']);
        Permission::create(['name' => 'view audit report all']);

        // Team permissions
        Permission::create(['name' => 'view all teams']);
        Permission::create(['name' => 'view team']);
        Permission::create(['name' => 'create team']);
        Permission::create(['name' => 'update team']);
        Permission::create(['name' => 'delete team']);
        Permission::create(['name' => 'restore team']);
        Permission::create(['name' => 'destroy team']);
        Permission::create(['name' => 'add staff']);
        Permission::create(['name' => 'remove staff']);

        // Observation Permission
        Permission::create(['name' => 'view all observations']);
        Permission::create(['name' => 'view observation']);
        Permission::create(['name' => 'create observation']);
        Permission::create(['name' => 'update observation']);
        Permission::create(['name' => 'delete observation']);
        Permission::create(['name' => 'restore observation']);
        Permission::create(['name' => 'destroy observation']);

        // finding permissions
        Permission::create(['name' => 'view all findings']);
        Permission::create(['name' => 'view finding']);
        Permission::create(['name' => 'create finding']);
        Permission::create(['name' => 'update finding']);
        Permission::create(['name' => 'delete finding']);
        Permission::create(['name' => 'restore finding']);
        Permission::create(['name' => 'destroy finding']);

        // Surcharge permissions
        Permission::create(['name' => 'view all surcharges']);
        Permission::create(['name' => 'view surcharge']);
        Permission::create(['name' => 'create surcharge']);

        // Department permissions
        Permission::create(['name' => 'view all departments']);
        Permission::create(['name' => 'view department']);
        Permission::create(['name' => 'create department']);
        Permission::create(['name' => 'update department']);
        Permission::create(['name' => 'delete department']);
        Permission::create(['name' => 'restore department']);
        Permission::create(['name' => 'destroy department']);

        // Unit permissions
        Permission::create(['name' => 'view all units']);
        Permission::create(['name' => 'view unit']);
        Permission::create(['name' => 'create unit']);
        Permission::create(['name' => 'update unit']);
        Permission::create(['name' => 'delete unit']);
        Permission::create(['name' => 'restore unit']);
        Permission::create(['name' => 'destroy unit']);

        //

        // action permissions
        Permission::create(['name' => 'view all actions']);
        Permission::create(['name' => 'view action']);
        Permission::create(['name' => 'create action']);
        Permission::create(['name' => 'update action']);
        Permission::create(['name' => 'delete action']);
        Permission::create(['name' => 'restore action']);
        Permission::create(['name' => 'destroy action']);

        // follow-up permissions
        Permission::create(['name' => 'view all follow-ups']);
        Permission::create(['name' => 'view follow-up']);
        Permission::create(['name' => 'create follow-up']);
        Permission::create(['name' => 'update follow-up']);
        Permission::create(['name' => 'delete follow-up']);
        Permission::create(['name' => 'restore follow-up']);
        Permission::create(['name' => 'destroy follow-up']);

        // cause permissions
        Permission::create(['name' => 'create cause']);
        Permission::create(['name' => 'update cause']);
        Permission::create(['name' => 'delete cause']);
        Permission::create(['name' => 'restore cause']);
        Permission::create(['name' => 'destroy cause']);

        // effect permissions
        Permission::create(['name' => 'create effect']);
        Permission::create(['name' => 'update effect']);
        Permission::create(['name' => 'delete effect']);
        Permission::create(['name' => 'restore effect']);
        Permission::create(['name' => 'destroy effect']);

        // region permissions
        Permission::create(['name' => 'view all regions']);
        Permission::create(['name' => 'view region']);
        Permission::create(['name' => 'create region']);
        Permission::create(['name' => 'update region']);
        Permission::create(['name' => 'delete region']);
        Permission::create(['name' => 'restore region']);
        Permission::create(['name' => 'destroy region']);

        // district permissions
        Permission::create(['name' => 'view all districts']);
        Permission::create(['name' => 'view district']);
        Permission::create(['name' => 'create district']);
        Permission::create(['name' => 'update district']);
        Permission::create(['name' => 'delete district']);
        Permission::create(['name' => 'restore district']);
        Permission::create(['name' => 'destroy district']);

        //  report permissions
        Permission::create(['name' => 'view all reports']);
        Permission::create(['name' => 'view report']);
        Permission::create(['name' => 'create report']);
        Permission::create(['name' => 'update report']);
        Permission::create(['name' => 'delete report']);
        Permission::create(['name' => 'restore report']);
        Permission::create(['name' => 'destroy report']);
        Permission::create(['name' => 'upload report']);

        //  recovery permissions
        Permission::create(['name' => 'view all recoveries']);
        Permission::create(['name' => 'view recovery']);
        Permission::create(['name' => 'create recovery']);
        Permission::create(['name' => 'update recovery']);
        Permission::create(['name' => 'delete recovery']);
        Permission::create(['name' => 'restore recovery']);
        Permission::create(['name' => 'destroy recovery']);

        // parliament recommendation permission

        Permission::create(['name' => 'view all parliament recommendations']);
        Permission::create(['name' => 'view parliament recommendation']);
        Permission::create(['name' => 'create parliament recommendation']);
        Permission::create(['name' => 'update parliament recommendation']);
        Permission::create(['name' => 'delete parliament recommendation']);
        Permission::create(['name' => 'restore parliament recommendation']);
        Permission::create(['name' => 'destroy parliament recommendation']);

        // user permissions
        Permission::create(['name' => 'view all users']);
        Permission::create(['name' => 'view user']);
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'update user']);
        Permission::create(['name' => 'delete user']);
        Permission::create(['name' => 'restore user']);
        Permission::create(['name' => 'destroy user']);

        // create roles and assign created permissions

        // this can be done as separate statements

        Role::create(['name' => 'Super Administrator'])
            ->givePermissionTo(Permission::all());
        Role::create(['name' => 'System Administrator'])
            ->givePermissionTo([
                'view all users',
                'view user',
                'create user',
                'update user',
                'delete user',
                'view all staff',
                'view staff',
                'create staff',
                'update staff',
                'delete staff',
                'view all institutions',
                'view institution',
                'create institution',
                'update institution',
                'delete institution',
                'restore institution',
                'restore audit',
                'restore audit',
                'restore observation',
                'restore finding',
                'restore recovery',
                'restore report',
                'restore parliament recommendation',
                'restore user',
                'restore staff',
                'restore document',
                'restore status',
                'restore action',
                'view all regions',
                'view region',
                'create region',
                'update region',
                'delete region',
                'restore region',
                'view all districts',
                'view district',
                'create district',
                'update district',
                'delete district',
                'restore district',
                'view all departments',
                'view department',
                'create department',
                'update department',
                'delete department',
                'restore department',
                'view all units',
                'view unit',
                'create unit',
                'update unit',
                'delete unit',
                'restore unit',
                'restore action',
            ]);
        Role::create(['name' => 'user']);
        Role::create(['name' => 'Staff'])
            ->givePermissionTo([
                'view all audits',
                'view audit',
                'view all observations',
                'view observation',
                'view all findings',
                'view finding',
                'view all actions',
                'view action',
                'view all recoveries',
                'view recovery',
                'view all reports',
                'view report',
                'view all parliament recommendations',
                'view parliament recommendation',
                'view all documents',
                'view document',
            ]);
        Role::create(['name' => 'Regional Auditor'])
            ->givePermissionTo([
                'view all staff',
                'view staff',
                'create report',
                'update report',
                'update report',
                'upload report',
                'create document',
                'update document',
                'delete document',
                'create action',
                'update action',
                'delete action',
                'create surcharge',
            ]);
        Role::create(['name' => 'Unit Head'])
            ->givePermissionTo([
                'view all staff',
                'view staff',
                'create report',
                'update report',
                'delete report',
                'restore report',
                'upload report',
                'create document',
                'update document',
                'delete document',
                'create action',
                'update action',
                'delete action',
                'create surcharge',
            ]);
        Role::create(['name' => 'District Auditor'])
            ->givePermissionTo([
                'view all staff',
                'view staff',
                'create report',
                'update report',
                'delete report',
                'restore report',
                'upload report',
                'create document',
                'update document',
                'delete document',
                'create action',
                'update action',
                'delete action',
                'create surcharge',
            ]);
        Role::create(['name' => 'Team Leader'])
            ->givePermissionTo([
                'view staff',
                'create report',
                'update report',
                'upload report',
                'create document',
                'update document',
                'delete document',
                'create action',
                'update action',
                'delete action',
                'create surcharge',
            ]);
        // Role::create(['name' => 'Team Member'])
        //     ->givePermissionTo([]);
        // Role::create(['name' => 'Auditee'])
        //     ->givePermissionTo([]);
        Role::create(['name' => 'Quality Manager'])
            ->givePermissionTo([]);
        // Role::create(['name' => 'Parliamentarian'])
        //     ->givePermissionTo([]);
        Role::create(['name' => 'Surcharge Manager'])
            ->givePermissionTo([
                'create surcharge',
            ]);
        Role::create(['name' => 'Surcharge Reviewer'])
            ->givePermissionTo([]);
        Role::create(['name' => 'Surcharge Approver'])
            ->givePermissionTo([]);
        Role::create(['name' => 'Surcharge Auditor'])
            ->givePermissionTo([]);

        // $role->givePermissionTo('update articles');
    }
}
