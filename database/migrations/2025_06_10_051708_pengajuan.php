<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id('id_pengajuan')->primary();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->foreign('id_user')->references('id')->on('user')->onDelete('cascade');
            $table->unsignedBigInteger('id_workshop')->nullable();
            $table->foreign('id_workshop')->references('id_workshop')->on('workshop')->onDelete('cascade');
            $table->unsignedBigInteger('id_pelayanan')->nullable();
            $table->foreign('id_pelayanan')->references('id_pelayanan')->on('pelayanan')->onDelete('cascade');
            $table->enum('status', ['diproses', 'disetujui', 'ditolak'])->default('diproses');
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};
