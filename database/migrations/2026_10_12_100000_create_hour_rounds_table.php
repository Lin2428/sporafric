<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tournées de relevés d'heures (ex-« impression de fiche ») : une tournée est
     * assignée à un technicien pour une date, chaque ligne = un groupe à relever.
     */
    public function up(): void
    {
        Schema::create('hour_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technicien_id')->constrained('techniciens');
            $table->date('date');
            $table->string('status')->default('0');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Historique des relevés : rattaché à une tournée (mobile) ou non (saisie bureau).
        Schema::create('hour_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hour_round_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('generator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('site')->nullable();
            $table->string('code_site')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->unsignedInteger('houres')->nullable();
            $table->integer('previous_houres')->nullable();
            $table->integer('prochain_visite')->nullable();
            $table->integer('next_vidange')->nullable();
            $table->dateTime('read_at')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['hour_round_id', 'generator_id']);
            $table->index(['generator_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hour_readings');
        Schema::dropIfExists('hour_rounds');
    }
};
