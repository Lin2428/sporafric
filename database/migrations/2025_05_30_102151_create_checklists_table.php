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
        Schema::create('checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generator_id');
            $table->foreignId('contract_id')->nullable();
            $table->foreignId('devis_id')->nullable();
            $table->foreignId('technicien_id');
            $table->string('is_clean')->nullable();
            $table->string('is_functional')->nullable();
            $table->float('electrical_value')->nullable();
            $table->string('is_maintained')->nullable();
            $table->float('mechanical_value')->nullable();
            $table->string('hour_number')->nullable();
            $table->string('next_vidange')->nullable();
            $table->int('technicien_id_after');
            $table->string('is_clean_after')->nullable();
            $table->string('is_functional_after')->nullable();
            $table->float('electrical_value_after')->nullable();
            $table->string('is_maintained_after')->nullable();
            $table->float('mechanical_value_after')->nullable();
            $table->string('hour_number_after')->nullable();
            $table->string('next_vidange_after')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklists');
    }
};
