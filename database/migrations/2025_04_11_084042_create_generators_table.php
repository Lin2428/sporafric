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
            $table->integer('odoo_id')->nullable();
            $table->string('name')->nullable();
            $table->integer('type')->default(2);
            $table->string('image')->nullable();
            $table->string('reference')->nullable();
            $table->double('power')->nullable();
            $table->double('voltage')->nullable();
            $table->double('frequency')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('start-up')->nullable();
            $table->integer('status')->default(0)->nullable();
            $table->string('houres')->nullable();
            $table->integer('next_vidange')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
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
