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
    i.id,
    i.contract_id,
    i.identifiant,
    i.created_at AS intervention_at,
    i.type AS type_intervention,
    inf.devis_montant,
    c.site,
    c.code_site,
    c.forfait,
    c.start_date AS contract_start_at,
    cu.name AS customer_name,

    -- Gestion du générateur
    COALESCE(cg.generator_id, c.generator_id) AS generator_id,
    COALESCE(g.name, '-') AS generator_name,

    -- Sous-requête pièces
    IFNULL(pieces_data.total_pieces, 0) AS total_pieces,
    IFNULL(pieces_data.montant_piece, 0) AS montant_piece,
    IFNULL(pieces_data.pieces, '') AS pieces,

    -- Sous-requête techniciens
    IFNULL(techs.techniciens, '') AS techniciens,

    -- Infos durée contrat et mensualité
    TIMESTAMPDIFF(MONTH, c.start_date, c.end_date) AS duree_contrat,
    TIMESTAMPDIFF(MONTH, c.start_date, NOW()) AS mois_ecoules,
    TIMESTAMPDIFF(MONTH, c.start_date, NOW()) * c.forfait AS montant_paye,

    -- Ajout du champ occupation seulement si contract_generators est utilisé
    TIMESTAMPDIFF(DAY, cg.created_at, cg.updated_at) AS occupation

FROM interventions i

-- Joins communs
LEFT JOIN contracts c ON c.id = i.contract_id
LEFT JOIN customers cu ON cu.id = c.customer_id
LEFT JOIN intervention_infos inf ON inf.id = i.id

-- Jointure avec contract_generators
LEFT JOIN contract_generators cg ON cg.contract_id = i.contract_id

-- Gestion du générateur (priorité à contract_generators)
LEFT JOIN generators g ON g.id = COALESCE(cg.generator_id, c.generator_id)

-- Sous-requête pièces
LEFT JOIN (
    SELECT
        ip.intrvention_id AS intervention_id,
        SUM(ip.qty) AS total_pieces,
        SUM(ip.qty * ip.price) AS montant_piece,
        GROUP_CONCAT(DISTINCT CONCAT(p.reference, '(', ip.qty, ')') SEPARATOR ', ') AS pieces
    FROM intervention_pieces ip
    LEFT JOIN pieces p ON p.id = ip.piece_id
    GROUP BY ip.intrvention_id
) AS pieces_data ON pieces_data.intervention_id = i.id

-- Sous-requête techniciens
LEFT JOIN (
    SELECT
        it.intervention_id,
        GROUP_CONCAT(DISTINCT t.name SEPARATOR ', ') AS techniciens
    FROM intervention_techniciens it
    LEFT JOIN techniciens t ON t.id = it.technicien_id
    GROUP BY it.intervention_id
) AS techs ON techs.intervention_id = i.id

WHERE i.type_location = 1
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
