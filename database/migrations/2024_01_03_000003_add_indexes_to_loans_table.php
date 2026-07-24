<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $this->safeAddIndex($table, 'loans', 'user_id');
            $this->safeAddIndex($table, 'loans', 'book_id');
            $this->safeAddIndex($table, 'loans', 'loan_date');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['book_id']);
            $table->dropIndex(['loan_date']);
        });
    }

    private function safeAddIndex(Blueprint $table, string $tableName, string $column): void
    {
        try {
            $indexes = Schema::getIndexes($tableName);
            foreach ($indexes as $name => $index) {
                if (isset($index['columns']) && in_array($column, $index['columns'])) {
                    return;
                }
            }
            $table->index($column);
        } catch (\Exception $e) {
            try {
                $table->index($column);
            } catch (\Exception $e2) {
                // Index may already exist, ignore
            }
        }
    }
};
