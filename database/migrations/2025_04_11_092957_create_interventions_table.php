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
        Schema::create('interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id');
            $table->date('date_prise_appel');
            $table->date('date_planifiee');
            $table->integer('type');
            $table->string('identifiant');
            $table->string('description_panne');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('compteur');
            $table->string('fiche');
            $table->boolean('facturable');
            $table->boolean('astrinte');
            $table->boolean('status');
            $table->boolean('cancelled')->default(false);
            $table->string('raison')->nullable();
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
        Schema::dropIfExists('interventions');
    }
};
