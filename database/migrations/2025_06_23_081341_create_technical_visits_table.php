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
        Schema::create('technical_visits', function (Blueprint $table) {
            $table->id();
            $table->boolean('type_service');
            $table->foreignId('contract_id')->nullable();
            $table->foreignId('devis_id')->nullable();
            $table->foreignId('generator_id');
            $table->boolean('control_1')->nullable();
            $table->boolean('control_2')->nullable();
            $table->boolean('control_3')->nullable();
            $table->boolean('control_4')->nullable();
            $table->boolean('control_5')->nullable();
            $table->boolean('control_6')->nullable();
            $table->boolean('control_7')->nullable();
            $table->boolean('control_8')->nullable();
            $table->boolean('control_9')->nullable();
            $table->boolean('control_10')->nullable();
            $table->boolean('control_11')->nullable();
            $table->boolean('control_12')->nullable();
            $table->boolean('control_13')->nullable();
            $table->boolean('control_14')->nullable();
            $table->boolean('control_15')->nullable();
            $table->boolean('control_16')->nullable();
            $table->integer('control_battery')->nullable();
            $table->float('control_circuit')->nullable();
            $table->json('control_tension')->nullable();
            $table->json('control_tension_2')->nullable();
            $table->json('control_intensite')->nullable();
            $table->float('control_frequence')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->dateTime('date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_visits');
    }
};
