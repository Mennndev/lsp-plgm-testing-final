<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->string('jabatan')->nullable()->after('instansi');
            $table->string('email_instansi')->nullable()->after('jabatan');
            $table->text('alamat_instansi')->nullable()->after('email_instansi');
            $table->string('telepon_instansi', 30)->nullable()->after('alamat_instansi');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropColumn([
                'jabatan',
                'email_instansi',
                'alamat_instansi',
                'telepon_instansi',
            ]);
        });
    }
};
