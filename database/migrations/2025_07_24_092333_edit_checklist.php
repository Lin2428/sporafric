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
        Schema::table('checklists', function (Blueprint $table) {
            $table->dropColumn('is_clean')->nullable();
            $table->dropColumn('is_functional')->nullable();
            $table->dropColumn('electrical_value')->nullable();
            $table->dropColumn('is_maintained')->nullable();
            $table->dropColumn('mechanical_value')->nullable();
            $table->dropColumn('hour_number')->nullable();
            $table->dropColumn('next_vidange')->nullable();

            $table->dropColumn('is_clean_after')->nullable();
            $table->dropColumn('is_functional_after')->nullable();
            $table->dropColumn('electrical_value_after')->nullable();
            $table->dropColumn('is_maintained_after')->nullable();
            $table->dropColumn('mechanical_value_after')->nullable();
            $table->dropColumn('hour_number_after')->nullable();
            $table->dropColumn('next_vidange_after')->nullable();

            //Nouvelles columns
            $table->boolean('control_1')->nullable();
            $table->boolean('control_2')->nullable();
            $table->boolean('control_3')->nullable();
            $table->boolean('control_4')->nullable();
            $table->boolean('control_5')->nullable();
            $table->boolean('control_6')->nullable();
            $table->boolean('control_7')->nullable();
            $table->boolean('control_8')->nullable();
            $table->boolean('control_9')->nullable();
            $table->boolean('control_10')->nullable();
            $table->boolean('control_11')->nullable();
            $table->boolean('control_12')->nullable();
            $table->boolean('control_13')->nullable();
            $table->boolean('control_14')->nullable();

            $table->json('control_tension')->nullable();
            $table->json('control_tension_2')->nullable();
            $table->json('control_intensite')->nullable();
            $table->float('control_frequence')->nullable();

            $table->text('responsable')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklist', function (Blueprint $table) {
            $table->dropColumn('control_1');
            $table->dropColumn('control_2');
            $table->dropColumn('control_3');
            $table->dropColumn('control_4');
            $table->dropColumn('control_5');
            $table->dropColumn('control_6');
            $table->dropColumn('control_7');
            $table->dropColumn('control_8');
            $table->dropColumn('control_9');
            $table->dropColumn('control_10');
            $table->dropColumn('control_11');
            $table->dropColumn('control_12');
            $table->dropColumn('control_13');
            $table->dropColumn('control_14');

            $table->dropColumn('control_tension');
            $table->dropColumn('control_tension_2');
            $table->dropColumn('control_intensite');
            $table->dropColumn('control_frequence');

            $table->dropColumn('responsable');
        });
    }
};
