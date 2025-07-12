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
        CREATE OR REPLACE VIEW v_location_repport AS
SELECT
    i.id,
    i.devis_id,
    i.numero,
    i.created_at AS intervention_at,
    i.type AS type_intervention,
    inf.devis_montant,
    dg.site,
    dg.code_site,
    d.forfait,
    d.start_date AS devis_start_at,
    cu.name AS customer_name,

    -- Gestion du générateur
    i.generator_id,
    i.new_generator_id,
    COALESCE(g.name, '-') AS generator_name,
    COALESCE(g_new.name, '-') AS new_generator_name,

    -- Sous-requête pièces
    IFNULL(pieces_data.total_pieces, 0) AS total_pieces,
    IFNULL(pieces_data.montant_piece, 0) AS montant_piece,
    IFNULL(pieces_data.pieces, '') AS pieces,

    -- Sous-requête techniciens
    IFNULL(techs.techniciens, '') AS techniciens,

    -- Infos durée contrat et mensualité
    TIMESTAMPDIFF(DAY, d.start_date, d.end_date) AS duree_contrat,
    TIMESTAMPDIFF(DAY, d.start_date, NOW()) AS jour_ecoules,

    -- Ajout du champ occupation seulement si devis_generators est utilisé
    TIMESTAMPDIFF(DAY, dg.created_at, dg.updated_at) AS occupation

FROM interventions i

-- Joins communs
LEFT JOIN devis d ON d.id = i.devis_id
LEFT JOIN customers cu ON cu.id = d.customer_id
LEFT JOIN intervention_infos inf ON inf.intervention_id = i.id

-- Jointure avec devis_generators
LEFT JOIN devis_generators dg ON dg.devis_id = i.devis_id

-- Gestion du générateur (priorité à devis_generators)
LEFT JOIN generators g ON g.id = i.generator_id
LEFT JOIN generators g_new ON g_new.id = i.new_generator_id

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

WHERE i.type_service = 0 
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_location_repport");
    }
};
