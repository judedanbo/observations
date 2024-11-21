<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('regions')->insert(
            [
                ['id' => 1, 'name' => 'Ashanti'],
                ['id' => 2, 'name' => 'Greater Accra'],
                ['id' => 3, 'name' => 'Ahafo'],
                ['id' => 4, 'name' => 'Bono'],
                ['id' => 5, 'name' => 'Bono East'],
                ['id' => 6, 'name' => 'Central'],
                ['id' => 7, 'name' => 'Eastern'],
                ['id' => 8, 'name' => 'North East'],
                ['id' => 9, 'name' => 'Northern'],
                ['id' => 10, 'name' => 'Oti'],
                ['id' => 11, 'name' => 'Savannah'],
                ['id' => 12, 'name' => 'Upper East'],
                ['id' => 13, 'name' => 'Upper West'],
                ['id' => 14, 'name' => 'Volta'],
                ['id' => 15, 'name' => 'Western'],
                ['id' => 16, 'name' => 'Western North'],
            ]
        );
    }
}
