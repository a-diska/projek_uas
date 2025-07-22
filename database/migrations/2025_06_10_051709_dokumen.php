<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id('id_dokumen')->primary();
            $table->unsignedBigInteger('id_pengajuan')->nullable();
            $table->foreign('id_pengajuan')->references('id_pengajuan')->on('pengajuan')->onDelete('cascade');
            $table->string('nama_file');
            $table->string('path');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
