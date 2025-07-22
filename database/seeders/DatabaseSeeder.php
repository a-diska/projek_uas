<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workshop;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            WorkshopSeeder::class,
            PelayananSeeder::class,
        ]);
    }
}
