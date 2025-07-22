<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('role')->insert([
            [
                'id_role' => 1,
                'nama_role' => 'admin',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_role' => 2,
                'nama_role' => 'verifikator',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_role' => 3,
                'nama_role' => 'peserta',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
