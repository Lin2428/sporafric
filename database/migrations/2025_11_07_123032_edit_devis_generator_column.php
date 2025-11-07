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
        // drop unique index if present, ignore error otherwise
        try {
            Schema::table('devis_generators', function (Blueprint $table) {
                $table->dropUnique('devis_generators_code_site_unique');
            });
        } catch (\Throwable $e) {
            // index might not exist – skip
        }

        // require doctrine/dbal for ->change()
        Schema::table('devis_generators', function (Blueprint $table) {
            $table->string('code_site')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devis_generators', function (Blueprint $table) {
            $table->string('code_site')->nullable(false)->change();
            $table->unique('code_site');
        });
    }
};
