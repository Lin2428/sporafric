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
            $table->boolean('type')->default(false);
            $table->boolean('is_clean')->default(false);
            $table->boolean('is_functional')->default(false);
            $table->float('electrical_value')->nullable();
            $table->boolean('is_maintained')->default(false);
            $table->float('mechanical_value')->nullable();
            $table->string('hour_number')->nullable();
            $table->string('next_vidange')->nullable();
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
