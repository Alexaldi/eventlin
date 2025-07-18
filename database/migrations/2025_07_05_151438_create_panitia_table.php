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
        Schema::create('panitia', function (Blueprint $table) {
            $table->id('id_panitia');
            $table->unsignedBigInteger('id_divisi')->nullable();
            $table->foreign('id_divisi')->references('id_divisi')->on('divisis')->onDelete('cascade');
            $table->unsignedBigInteger('id_proposal');
            $table->foreign('id_proposal')->references('id_proposal')->on('proposals')->onDelete('cascade');
            $table->string('nama_panitia');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('jabatan_panitia', ['Ketua', 'Sekretaris', 'Bendahara', 'Panitia','akademik']);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panitia');
    }
};
