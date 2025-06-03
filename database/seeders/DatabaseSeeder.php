<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Department;
use App\Models\Office;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            OfficeRolesAndPermissionsSeeder::class,
            UserSeeder::class,
            RegionSeeder::class,
            DistrictSeeder::class,
            DepartmentSeeder::class,
            OfficeSeeder::class,
            UnitSeeder::class,
        ]);
    }
}
