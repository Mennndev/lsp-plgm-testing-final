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
            Schema::create('sertifikat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengajuan_skema_id')
                ->constrained('pengajuan_skema')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('nomor_sertifikat')->unique();
            $table->string('jenis_bukti')->nullable();
            $table->date('tanggal_terbit');
            $table->date('tanggal_berlaku_sampai');
            $table->string('file_sertifikat')->nullable();

            $table->enum('status', ['aktif', 'expired', 'dicabut'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
};
