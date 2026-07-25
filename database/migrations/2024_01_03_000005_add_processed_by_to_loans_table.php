<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('loans', 'processed_by')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->unsignedBigInteger('processed_by')->nullable()->after('status_denda');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('loans', 'processed_by')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->dropColumn('processed_by');
            });
        }
    }
};
