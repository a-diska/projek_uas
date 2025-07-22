<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PelayananSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pelayanan')->insert([
            [
                'nama_pelayanan' => 'sertifikasi guru',
                'deskripsi' => 'program sertifikasi guru',
                'status' => 'aktif',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_pelayanan' => 'pelatihan kurikulum merdeka',
                'deskripsi' => 'pelatihan tentang kurikulum merdeka untuk guru',
                'status' => 'aktif',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_pelayanan' => 'pendampingan digital',
                'deskripsi' => 'bimbingan penggunaan teknologi dalam pembelajaran',
                'status' => 'nonaktif',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_pelayanan' => 'manajemen kelas',
                'deskripsi' => 'workshop tentang pengelolaan kelas yang baik',
                'status' => 'nonaktif',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_pelayanan' => 'peningkatan literasi',
                'deskripsi' => 'pelatihan untuk meningkatkan literasi siswa',
                'status' => 'aktif',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
