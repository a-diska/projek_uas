<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role', function (Blueprint $table) {
            $table->bigIncrements('id_role'); 
            $table->string('nama_role', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role');
    }
};
