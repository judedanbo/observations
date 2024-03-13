<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'id' => 1,
                'name' => 'Scheduled',
                'description' => 'Audit scheduled but not started yet',
            ],
            [
                'id' => 2,
                'name' => 'In Progress',
                'description' => 'Audit in progress',
            ],
            [
                'id' => 3,
                'name' => 'Completed',
                'description' => 'Audit is completed and Management is issued',
            ],
            [
                'id' => 4,
                'name' => 'Terminated',
                'description' => 'Audit terminated or cancelled before completion',
            ],
        ];
        \App\Models\Status::insert($statuses);
    }
}
