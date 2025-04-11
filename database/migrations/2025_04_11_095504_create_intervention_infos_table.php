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
        Schema::create('intervention_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intervention_id');
            $table->string('devis_numero');
            $table->date('devis_date');
            $table->integer('devis_montant');
            $table->string('bc_numero');
            $table->date('bc_date');
            $table->string('bc_fiche');
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
        Schema::dropIfExists('intervention_infos');
    }
};
