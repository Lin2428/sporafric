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
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->integer('odoo_id')->nullable();
            $table->foreignId('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('number')->unique();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(value: true);
            $table->string('state')->nullable();
            $table->integer('forfait')->nullable();
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
        Schema::dropIfExists('devis');
    }
};
