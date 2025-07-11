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
        Schema::table('interventions', function (Blueprint $table) {
            $table->integer('montant')->nullable();
            $table->string('numero')->unique()->nullable();
            $table->foreignId('old_generator_id')->nullable()->constrained('generators');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intervention', function (Blueprint $table) {
            $table->dropColumn('montant');
            $table->dropColumn('numero');
            $table->dropColumn('old_generator_id');
        });
    }
};
