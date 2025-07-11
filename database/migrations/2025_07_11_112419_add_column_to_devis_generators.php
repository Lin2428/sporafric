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
        Schema::table('devis_generators', function (Blueprint $table) {
             $table->integer('forfait')->default('0');
             $table->integer('old_generator_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devis_generators', function (Blueprint $table) {
            $table->dropColumn('forfait');
            $table->dropColumn('old_generator_id');
        });
    }
};
