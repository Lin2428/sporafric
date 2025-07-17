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
    i.numero,
    i.created_at AS intervention_at,
    i.type AS type_intervention,
    i.montant,
    cg.site,
    cg.code_site,
    cg.forfait,
    c.start_date AS contract_start_at,
    cu.name AS customer_name,

    -- Gestion du générateur
    i.new_generator_id,
    i.generator_id,
    COALESCE(g.name, '-') AS generator_name,

    -- Sous-requête pièces
    IFNULL(pieces_data.total_pieces, 0) AS total_pieces,
    IFNULL(pieces_data.montant_piece, 0) AS montant_piece,
    IFNULL(pieces_data.pieces, '') AS pieces,

    -- Sous-requête techniciens
    IFNULL(techs.techniciens, '') AS techniciens,

    -- Infos durée contrat et mensualité
    TIMESTAMPDIFF(MONTH, c.start_date, IFNULL(c.end_date, CURDATE())) AS duree_contrat,
    TIMESTAMPDIFF(MONTH, c.start_date, NOW()) AS mois_ecoules,
    TIMESTAMPDIFF(MONTH, c.start_date, NOW()) * cg.forfait AS montant_paye,

    -- Durée d'occupation du générateur
    TIMESTAMPDIFF(DAY, cg.created_at, cg.updated_at) AS occupation

FROM interventions i

-- Joins communs
LEFT JOIN contracts c ON c.id = i.contract_id
LEFT JOIN customers cu ON cu.id = c.customer_id
LEFT JOIN intervention_infos inf ON inf.intervention_id = i.id

-- Jointure filtrée avec contract_generators (1 seul par contrat)
LEFT JOIN (
    SELECT *
    FROM (
        SELECT *,
               ROW_NUMBER() OVER (
                   PARTITION BY contract_id, generator_id
                   ORDER BY created_at DESC
               ) AS rn
        FROM contract_generators
    ) AS ranked_cg
    WHERE rn = 1
) AS cg ON cg.contract_id = i.contract_id AND cg.generator_id = i.generator_id

-- Gestion du générateur (lié à contract_generator)
LEFT JOIN generators g ON g.id = cg.generator_id

-- Sous-requête pièces
LEFT JOIN (
    SELECT
        ip.intervention_id AS intervention_id,
        SUM(ip.qty) AS total_pieces,
        SUM(ip.qty * ip.price) AS montant_piece,
        GROUP_CONCAT(DISTINCT CONCAT(p.reference, '(', ip.qty, ')') SEPARATOR ', ') AS pieces
    FROM intervention_pieces ip
    LEFT JOIN pieces p ON p.id = ip.piece_id
    GROUP BY ip.intervention_id
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

WHERE i.type_service = 1 
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
