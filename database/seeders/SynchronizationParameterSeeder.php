<?php

namespace Database\Seeders;

use App\Enum\FieldType;
use App\Enum\SynchronizationParametersType;
use App\Models\SynchronizeParameter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SynchronizationParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $operators = [
            [
                'model' => SynchronizationParametersType::TECHNICIEN->value,
                'field' => 'department_id',
                'field_type' => FieldType::INT->value,
                'operator_filter_id' => 1,
                'value' => ["6"],
                'is_active' => 1,
            ],
            [
                'model' => SynchronizationParametersType::TECHNICIEN->value,
                'field' => 'job_id',
                'field_type' => FieldType::INT->value,
                'operator_filter_id' => 11,
                'value' => ["4","26","27","28"],
                'is_active' => 1,
            ],
            [
                'model' => SynchronizationParametersType::GENERATOR->value,
                'field' => 'categ_id',
                'field_type' => FieldType::INT->value,
                'operator_filter_id' => 1,
                'value' => ["240"],
                'is_active' => 1,
            ],
            [
                'model' => SynchronizationParametersType::GENERATOR->value,
                'field' => 'active',
                'field_type' => FieldType::BOOLEAN->value,
                'operator_filter_id' => 1,
                'value' => ["true"],
                'is_active' => 1,
            ],
            [
                'model' => SynchronizationParametersType::DEVIS->value,
                'field' => 'is_rental_order',
                'field_type' => FieldType::BOOLEAN->value,
                'operator_filter_id' => 1,
                'value' => ["true"],
                'is_active' => 1,
            ],
            [
                'model' => SynchronizationParametersType::DEVIS->value,
                'field' => 'state',
                'field_type' => FieldType::STRING->value,
                'operator_filter_id' => 12,
                'value' => ["cancel"],
                'is_active' => 1,
            ],
            [
                'model' => SynchronizationParametersType::CONSO_INTERNE->value,
                'field' => 'conso_interne',
                'field_type' => FieldType::BOOLEAN->value,
                'operator_filter_id' => 1,
                'value' => ["true"],
                'is_active' => 1,
            ],
            [
                'model' => SynchronizationParametersType::CONSO_INTERNE->value,
                'field' => 'state',
                'field_type' => FieldType::STRING->value,
                'operator_filter_id' => 12,
                'value' => ["cancel"],
                'is_active' => 1,
            ],
            [
                'id' => 9,
                'model' => SynchronizationParametersType::CONSO_INTERNE->value,
                'field' => 'partner_id',
                'field_type' => FieldType::INT->value,
                'operator_filter_id' => 1,
                'value' => ["3969"],
                'is_active' => 1,
            ],
            [
                'model' => SynchronizationParametersType::PIECE->value,
                'field' => 'categ_id',
                'field_type' => FieldType::INT->value,
                'operator_filter_id' => 11,
                'value' => ["80","239","107","284","302","59","83","5902"],
                'is_active' => 1,
            ],
        ];

        foreach ($operators as $operator) {
            SynchronizeParameter::firstOrCreate($operator);
        }
    }
}
