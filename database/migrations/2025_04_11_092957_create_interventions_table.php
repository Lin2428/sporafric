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
            $table->integer('type_location');
            $table->foreignId('contract_id')->nullable();
                 $table->foreignId('devis_id')->nullable();
            $table->foreignId('customer_id')->nullable();
            $table->date('date_prise_appel');
            $table->date('date_planifiee');
            $table->integer('type');
            $table->string('identifiant');
            $table->text('description_panne')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('compteur')->nullable();
            $table->string('fiche')->nullable();
            $table->boolean('facturable')->nullable();
            $table->boolean('astrinte')->nullable();
            $table->boolean('status')->nullable();
            $table->boolean('cancelled')->default(false);
            $table->string('raison')->nullable();
            $table->string('generator')->nullable();
            $table->string('power')->nullable();
            $table->string('serial_number')->nullable();
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
