<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pengajuan_skema MODIFY status ENUM('draft','pending','approved','rejected','paid') NOT NULL DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('pengajuan_skema')->where('status', 'paid')->update(['status' => 'approved']);
            DB::statement("ALTER TABLE pengajuan_skema MODIFY status ENUM('draft','pending','approved','rejected') NOT NULL DEFAULT 'draft'");
        }
    }
};
