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
        Schema::create('customer_adresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->foreignId('country_id');
            $table->foreignId('city_id');
            $table->foreignId('district_id')->nullable();
            $table->foreignId('quartier_id')->nullable();
            $table->string('address');
            $table->string('postal_code')->nullable();
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
        Schema::dropIfExists('customer_adresses');
    }
};
