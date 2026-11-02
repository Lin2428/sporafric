<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Périodes d'utilisation des GE de location, importées d'Odoo (une ligne par
     * sale.order.line « is_rental »). Les dates réelles viennent des mouvements de
     * stock « Mouvement de location » (Stock → Rental = départ, Rental → Stock = retour).
     */
    public function up(): void
    {
        Schema::create('rental_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('odoo_line_id')->unique();
            $table->unsignedBigInteger('odoo_order_id')->index();
            $table->foreignId('generator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('devis_id')->nullable()->constrained('devis')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number')->nullable();
            $table->string('order_state')->nullable();      // draft / sent / sale / done / cancel
            $table->string('rental_status')->nullable();    // draft / pickup / return / returned / cancel
            $table->dateTime('planned_start')->nullable();
            $table->dateTime('planned_return')->nullable();
            $table->dateTime('picked_up_at')->nullable();   // départ réel
            $table->dateTime('returned_at')->nullable();    // retour réel
            $table->decimal('amount_untaxed', 15, 2)->default(0);
            $table->string('invoice_status')->nullable();
            $table->string('invoice_numbers')->nullable();
            $table->timestamps();

            $table->index(['generator_id', 'planned_start']);
        });

        // Prix de revient HT du GE (Odoo standard_price, ou saisi dans la GMAO si absent d'Odoo).
        Schema::table('generators', function (Blueprint $table) {
            $table->decimal('pr', 15, 2)->nullable()->after('power');
        });

        // Historique des changements de statut (à partir de maintenant).
        Schema::create('generator_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generator_id')->constrained()->cascadeOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        // Paramètres applicatifs simples (clé / valeur JSON).
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
        Schema::dropIfExists('generator_status_logs');
        Schema::table('generators', function (Blueprint $table) {
            $table->dropColumn('pr');
        });
        Schema::dropIfExists('rental_periods');
    }
};
