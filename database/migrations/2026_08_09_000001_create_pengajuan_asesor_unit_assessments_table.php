<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_asesor_unit_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_skema_id')
                ->constrained('pengajuan_skema')
                ->cascadeOnDelete();
            $table->foreignId('unit_kompetensi_id')
                ->constrained('unit_kompetensis')
                ->cascadeOnDelete();
            $table->foreignId('asesor_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->enum('nilai', ['K', 'BK']);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(
                ['pengajuan_skema_id', 'unit_kompetensi_id', 'asesor_id'],
                'pau_pengajuan_unit_asesor_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_asesor_unit_assessments');
    }
};
