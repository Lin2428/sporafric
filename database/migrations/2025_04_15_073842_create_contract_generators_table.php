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
        Schema::create('contract_generators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained();
            $table->foreignId('generator_id')->nullable();
            $table->string('site')->nullable();
            $table->string('code_site')->nullable();
            $table->boolean('status')->default(false);
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
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
        Schema::dropIfExists('contract_generators');
    }
};
