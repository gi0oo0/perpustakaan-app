<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('loans', 'denda')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->integer('denda')->default(0)->after('returned_at');
            });
        }
        if (!Schema::hasColumn('loans', 'status_denda')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->string('status_denda')->default('belum_bayar')->after('denda');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('loans', 'denda')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->dropColumn(['denda', 'status_denda']);
            });
        }
    }
};
