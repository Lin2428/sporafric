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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->integer('odoo_id')->unique();
            $table->string('name');
            $table->string('city')->nullable();
            $table->string('contact_c_name')->nullable();
            $table->string('contact_c_email')->nullable();
            $table->string('contact_c_phone')->nullable();
            $table->string('contact_l_name')->nullable();
            $table->string('contact_l_email')->nullable();
            $table->string('contact_l_phone')->nullable();
            $table->string('logo')->nullable();
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
        Schema::dropIfExists('customers');
    }
};
