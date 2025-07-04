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
        CREATE OR REPLACE VIEW v_revenu_monsuel AS
SELECT
    mois.mois,

    -- Total des devis (forfait payé une seule fois)
    (
        SELECT IFNULL(SUM(forfait), 0)
        FROM devis d
        WHERE DATE_FORMAT(d.start_date, '%Y-%m') = mois.mois
    ) AS revenu_devis,

    -- Total des contrats (forfait payé chaque mois entre start_date et end_date)
    (
        SELECT IFNULL(SUM(c.forfait), 0)
        FROM contracts c
        WHERE mois.mois BETWEEN DATE_FORMAT(c.start_date, '%Y-%m') AND DATE_FORMAT(c.end_date, '%Y-%m')
    ) AS revenu_contract,

    -- Total des interventions (via infos → devis_montant)
    (
        SELECT IFNULL(SUM(iinfo.devis_montant), 0)
        FROM interventions i
        JOIN intervention_infos iinfo ON i.id = iinfo.intervention_id
        WHERE DATE_FORMAT(i.created_at, '%Y-%m') = mois.mois
    ) AS revenu_intervention,

    -- Total des pièces (pivot: intervention_pieces)
    (
        SELECT IFNULL(SUM(ip.qty * ip.price), 0)
        FROM interventions i
        JOIN intervention_pieces ip ON i.id = ip.intrvention_id
        WHERE DATE_FORMAT(i.created_at, '%Y-%m') = mois.mois
    ) AS revenu_pieces

FROM (
    -- Générer chaque mois depuis janvier jusqu’au mois actuel
    SELECT DATE_FORMAT(DATE_ADD('2025-01-01', INTERVAL n MONTH), '%Y-%m') AS mois
    FROM (
        SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION
        SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11
    ) AS nums
    WHERE DATE_ADD('2025-01-01', INTERVAL n MONTH) <= LAST_DAY(CURRENT_DATE)
) AS mois
ORDER BY mois.mois;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_revenu_monsuel");
    }
};
