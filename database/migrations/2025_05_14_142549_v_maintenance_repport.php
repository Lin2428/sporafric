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
        DB::statement("
        CREATE OR REPLACE VIEW v_maintenance_repport AS
SELECT
    COUNT(DISTINCT interventions.id) AS total_intevention,
    interventions.contract_id,
    GROUP_CONCAT(DISTINCT interventions.identifiant SEPARATOR ', ') AS identifiants,
    contracts.code_site,
    contracts.forfait,
    customers.name AS customer_name,
    generators.name AS generator_name,
    SUM(DISTINCT intervention_pieces.qty) AS total_pieces,
    SUM(DISTINCT intervention_pieces.qty * intervention_pieces.price) AS montant_piece,
    GROUP_CONCAT(DISTINCT CONCAT(pieces.reference, '(', intervention_pieces.qty, ')') SEPARATOR ', ') AS pieces,
    GROUP_CONCAT(DISTINCT techniciens.name SEPARATOR ', ') AS techniciens,
    TIMESTAMPDIFF(MONTH, contracts.start_date, contracts.end_date) AS duree_contrat,
    TIMESTAMPDIFF(MONTH, contracts.start_date, NOW()) AS mois_ecoules,
    TIMESTAMPDIFF(MONTH, contracts.start_date, NOW()) * contracts.forfait AS montant_deja_paye
FROM interventions 
LEFT JOIN contracts ON contracts.id = interventions.contract_id
LEFT JOIN intervention_infos ON intervention_infos.intervention_id = interventions.id
LEFT JOIN intervention_pieces ON intervention_pieces.intrvention_id = interventions.id
LEFT JOIN intervention_techniciens ON intervention_techniciens.intervention_id = interventions.id
LEFT JOIN techniciens ON techniciens.id = intervention_techniciens.technicien_id
LEFT JOIN pieces ON pieces.id = intervention_pieces.piece_id
LEFT JOIN customers ON customers.id = contracts.customer_id
LEFT JOIN generators ON generators.id = contracts.generator_id
WHERE interventions.type_location = 1
GROUP BY 
    interventions.contract_id,
    contracts.code_site,
    contracts.forfait,
    customers.name,
    generators.name,
    contracts.start_date,
    contracts.end_date
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_maintenance_repport");
    }
};
