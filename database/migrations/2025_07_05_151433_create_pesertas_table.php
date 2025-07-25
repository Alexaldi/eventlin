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
        Schema::create('pesertas', function (Blueprint $table) {
            $table->string('nim')->primary();
            $table->unsignedBigInteger('id_proposal');
            $table->foreign('id_proposal')->references('id_proposal')->on('proposals')->onDelete('cascade');
            $table->string('nama_peserta');
            $table->string('email');
            $table->string('password');
            $table->rememberToken(); // sama dengan $table->string('remember_token', 100)->nullable();
            $table->date('tanggal_pendaftaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesertas');
    }
};
