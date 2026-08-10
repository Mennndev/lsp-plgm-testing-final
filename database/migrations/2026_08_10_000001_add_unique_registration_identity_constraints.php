<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicatePhones = DB::table('users')
            ->select('no_hp', DB::raw('COUNT(*) as total'))
            ->whereNotNull('no_hp')
            ->where('no_hp', '<>', '')
            ->groupBy('no_hp')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('no_hp');

        if ($duplicatePhones->isNotEmpty()) {
            throw new \RuntimeException(
                'Tidak dapat menambahkan unique constraint users.no_hp karena ada nomor handphone duplikat: '
                .$duplicatePhones->take(5)->implode(', ')
            );
        }

        $duplicateNiks = DB::table('pendaftarans')
            ->select('no_ktp', DB::raw('COUNT(*) as total'))
            ->whereNotNull('no_ktp')
            ->where('no_ktp', '<>', '')
            ->groupBy('no_ktp')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('no_ktp');

        if ($duplicateNiks->isNotEmpty()) {
            throw new \RuntimeException(
                'Tidak dapat menambahkan unique constraint pendaftarans.no_ktp karena ada NIK duplikat: '
                .$duplicateNiks->take(5)->implode(', ')
            );
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unique('no_hp', 'users_no_hp_unique');
        });

        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->unique('no_ktp', 'pendaftarans_no_ktp_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_no_hp_unique');
        });

        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropUnique('pendaftarans_no_ktp_unique');
        });
    }
};
