<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert(
            [
                ['id' => 1, 'name' => 'Educational Institutions and District Assemblies Northern Zone', 'short_name' => 'EIDA NZ', 'description' => 'Educational Institutions and District Assemblies Northern Zone'],
                ['id' => 2, 'name' => 'Educational Institutions and District Assemblies Southern Zone', 'short_name' => 'EIDA SZ', 'description' => 'Educational Institutions and District Assemblies Southern Zone'],
                ['id' => 3, 'name' => 'Central Government Agency Department', 'short_name' => 'CGAD', 'description' => 'Central Government Agency Department'],
                ['id' => 4, 'name' => 'Commercial Audit Department', 'short_name' => 'CAD', 'description' => 'Commercial Audit Department'],
                ['id' => 5, 'name' => 'Performance and Special Audit', 'short_name' => 'PSAD', 'description' => 'Performance and Special Audit'],
            ]
        );
    }
}
