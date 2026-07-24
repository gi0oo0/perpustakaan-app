<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $hasColumn = Schema::hasColumn('users', 'role');
        if (!$hasColumn) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('user')->after('name');
            });
        }
    }

    public function down(): void
    {
        $hasColumn = Schema::hasColumn('users', 'role');
        if ($hasColumn) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
