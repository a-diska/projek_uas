<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_approval', function (Blueprint $table) {
            $table->id('id_log_approval')->primary();
            $table->unsignedBigInteger('id_pengajuan');
            $table->foreign('id_pengajuan')->references('id_pengajuan')->on('pengajuan')->onDelete('cascade');
            $table->unsignedBigInteger('id_verifikator');
            $table->foreign('id_verifikator')->references('id_verifikator')->on('verifikator')->onDelete('cascade');
            $table->enum('status', ['disetujui', 'ditolak']);
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_approval');
    }
};
