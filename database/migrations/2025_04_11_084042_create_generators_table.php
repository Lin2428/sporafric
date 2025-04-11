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
        Schema::create('generators', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->string('modele');
            $table->double('power');
            $table->double('voltage')->nullable();
            $table->double('frequency')->nullable();
            $table->double('serial_number')->nullable();
            $table->date('start-up')->nullable();
            $table->integer('status')->default(0)->nullable();
            $table->time('houres')->nullable();
            $table->date('next_vidange')->nullable();
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
        Schema::dropIfExists('generators');
    }
};
