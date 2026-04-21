<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OperatorFilter;

class OperatorFilterSeeder extends Seeder
{
    public function run()
    {
        $operators = [
            // Opérateurs de comparaison simples
            ['operator' => '=', 'label' => 'Égale à'],
            ['operator' => '!=', 'label' => 'Différent de'],
            ['operator' => '>', 'label' => 'Supérieur à'],
            ['operator' => '>=', 'label' => 'Supérieur ou égal à'],
            ['operator' => '<', 'label' => 'Inférieur à'],
            ['operator' => '<=', 'label' => 'Inférieur ou égal à'],

            // Opérateurs sur les chaînes (texte)
            ['operator' => 'ilike', 'label' => 'Contient'],
            ['operator' => 'not ilike', 'label' => 'Ne contient pas'],
            ['operator' => 'startswith', 'label' => 'Commence par'],
            ['operator' => 'endswith', 'label' => 'Finit par'],

            // Opérateurs d’appartenance
            ['operator' => 'in', 'label' => 'Dans la liste'],
            ['operator' => 'not in', 'label' => 'Pas dans la liste'],

            // Opérateurs logiques (pour info, souvent utilisés dans les requêtes complexes)
            ['operator' => 'and', 'label' => 'Et'],
            ['operator' => 'or', 'label' => 'Ou'],
            ['operator' => 'not', 'label' => 'Non'],
        ];

        foreach ($operators as $operator) {
            OperatorFilter::firstOrCreate($operator);
        }
    }
}
