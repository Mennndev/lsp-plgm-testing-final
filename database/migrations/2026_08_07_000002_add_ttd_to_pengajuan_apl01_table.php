<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pengajuan_apl01', 'ttd')) {
            Schema::table('pengajuan_apl01', function (Blueprint $table) {
                $table->longText('ttd')->nullable()->after('catatan');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pengajuan_apl01', 'ttd')) {
            Schema::table('pengajuan_apl01', function (Blueprint $table) {
                $table->dropColumn('ttd');
            });
        }
    }
};
