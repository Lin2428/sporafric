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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'technicien_id')) {
                $table->foreignId('technicien_id')
                    ->nullable()
                    ->unique()
                    ->after('phone')
                    ->constrained('techniciens')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'technicien_id')) {
                $table->dropConstrainedForeignId('technicien_id');
            }
        });
    }
};
