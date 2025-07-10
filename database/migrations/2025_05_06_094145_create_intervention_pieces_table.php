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
        Schema::create('intervention_pieces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intervention_id');
            $table->foreignId('generator_id')->nullable();
            $table->foreignId('piece_id');
            $table->integer('qty')->default(1);
            $table->integer('price')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervention_pieces');
    }
};
