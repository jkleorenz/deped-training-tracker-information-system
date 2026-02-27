<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CS Form No. 212 — Section II. Family Background, Section III. Educational Background
     */
    public function up(): void
    {
        $addIfMissing = function (Blueprint $table, string $name, string $type, ...$args) {
            if (! Schema::hasColumn('personal_data_sheets', $name)) {
                $table->{$type}($name, ...$args)->nullable();
            }
        };

        Schema::table('personal_data_sheets', function (Blueprint $table) use ($addIfMissing) {
            // Section II. Family Background — 22. Spouse
            $addIfMissing($table, 'spouse_surname', 'string', 100);
            $addIfMissing($table, 'spouse_first_name', 'string', 100);
            $addIfMissing($table, 'spouse_middle_name', 'string', 100);
            $addIfMissing($table, 'spouse_occupation', 'string', 255);
            $addIfMissing($table, 'spouse_employer_business_name', 'string', 255);
            $addIfMissing($table, 'spouse_business_address', 'string', 500);
            $addIfMissing($table, 'spouse_telephone', 'string', 50);
            $addIfMissing($table, 'children_names', 'text');
            $addIfMissing($table, 'father_surname', 'string', 100);
            $addIfMissing($table, 'father_first_name', 'string', 100);
            $addIfMissing($table, 'father_middle_name', 'string', 100);
            $addIfMissing($table, 'mother_surname', 'string', 100);
            $addIfMissing($table, 'mother_first_name', 'string', 100);
            $addIfMissing($table, 'mother_middle_name', 'string', 100);

            // Section III. Educational Background (per level)
            $levels = ['elem', 'secondary', 'voc', 'college', 'grad'];
            foreach ($levels as $pre) {
                $addIfMissing($table, "{$pre}_school", 'string', 255);
                $addIfMissing($table, "{$pre}_degree_course", 'string', 255);
                $addIfMissing($table, "{$pre}_period_from", 'string', 20);
                $addIfMissing($table, "{$pre}_period_to", 'string', 20);
                $addIfMissing($table, "{$pre}_highest_level_units", 'string', 255);
                $addIfMissing($table, "{$pre}_scholarship_honors", 'string', 255);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->dropColumn([
                'spouse_surname', 'spouse_first_name', 'spouse_middle_name',
                'spouse_occupation', 'spouse_employer_business_name', 'spouse_business_address', 'spouse_telephone',
                'children_names',
                'father_surname', 'father_first_name', 'father_middle_name',
                'mother_surname', 'mother_first_name', 'mother_middle_name',
            ]);
            $levels = ['elem', 'secondary', 'voc', 'college', 'grad'];
            foreach ($levels as $pre) {
                $table->dropColumn([
                    "{$pre}_school", "{$pre}_degree_course", "{$pre}_period_from", "{$pre}_period_to",
                    "{$pre}_highest_level_units", "{$pre}_scholarship_honors",
                ]);
            }
        });
    }
};
