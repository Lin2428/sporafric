<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Consommations internes Odoo (sale.order conso_interne) rattachées à un GE.
     * Comptées comme MAINTENANCE dans le rapport de location (jour M + coût).
     * Le GE n'est connu que par le texte des lignes (« N° SERIE : 23 00 49 51 »).
     */
    public function up(): void
    {
        Schema::create('internal_consumptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('odoo_order_id')->unique();
            $table->foreignId('generator_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number')->nullable();
            $table->string('order_state')->nullable();
            $table->dateTime('order_date')->nullable();
            $table->decimal('amount_untaxed', 15, 2)->default(0);
            $table->string('serial')->nullable();          // n° de série lu dans le texte
            $table->unsignedInteger('hours')->nullable();   // « 779 hrs » si présent
            $table->text('description')->nullable();        // libellé de l'intervention
            $table->timestamps();

            $table->index(['generator_id', 'order_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_consumptions');
    }
};
