<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkshopSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('workshop')->insert([
            [
                'nama_workshop' => 'strategi pembelajaran merdeka belajar',
                'tanggal_mulai' => '2025-04-01',
                'tanggal_selesai' => '2025-04-03',
                'lokasi' => 'balai pelatihan guru surabaya',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_workshop' => 'penguatan literasi dan numerasi dasar',
                'tanggal_mulai' => '2025-05-10',
                'tanggal_selesai' => '2025-05-12',
                'lokasi' => 'dinas pendidikan kab. probolinggo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_workshop' => 'digitalisasi pembelajaran berbasis LMS',
                'tanggal_mulai' => '2025-06-01',
                'tanggal_selesai' => '2025-06-02',
                'lokasi' => 'SDN sukapura 1, probolinggo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_workshop' => 'pengembangan media ajar interaktif',
                'tanggal_mulai' => '2025-03-15',
                'tanggal_selesai' => '2025-03-17',
                'lokasi' => 'gedung serbaguna GRAHA kraksaan',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_workshop' => 'manajemen kelas efektif dan inklusif',
                'tanggal_mulai' => '2025-06-05',
                'tanggal_selesai' => '2025-06-07',
                'lokasi' => 'SMAN 1 kraksaan, probolinggo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
