<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
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
                'description' => 'Audit is completed and report is issued',
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
