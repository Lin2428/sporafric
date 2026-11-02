<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Signature du client en fin d'intervention / de visite technique (app mobile)
     * + suivi de l'email de compte rendu envoyé au client.
     */
    public function up(): void
    {
        foreach (['interventions', 'technical_visits'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('signature_path')->nullable();       // storage/app/public/signatures/…png
                $table->string('signed_by')->nullable();            // nom du signataire
                $table->dateTime('signed_at')->nullable();
                $table->string('signature_refusal')->nullable();    // client absent / refus : motif
                $table->string('client_email')->nullable();         // destinataire du compte rendu
                $table->dateTime('report_sent_at')->nullable();
                $table->string('report_error')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['interventions', 'technical_visits'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn(['signature_path', 'signed_by', 'signed_at', 'signature_refusal', 'client_email', 'report_sent_at', 'report_error']);
            });
        }
    }
};
