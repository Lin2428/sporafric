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
        Schema::create('synchronize_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->string('field');
            $table->string('field_type');
            $table->foreignId('operator_filter_id')->constrained('operator_filters')->onDelete('cascade');
            $table->json('value');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('synchronize_parameters');
    }
};
