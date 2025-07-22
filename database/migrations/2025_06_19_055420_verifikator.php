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
        Schema::create('verifikator', function (Blueprint $table) {
            $table->id('id_verifikator')->primary();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->foreign('id_user')->references('id')->on('user')->onDelete('cascade');
            $table->tinyInteger('tahapan')->nullable();
            $table->string('jabatan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikator');
    }
};
