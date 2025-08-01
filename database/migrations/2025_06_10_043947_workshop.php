<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshop', function (Blueprint $table) {
            $table->id('id_workshop')->primary();
            $table->string('nama_workshop');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('lokasi', 150);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop');
    }
};
