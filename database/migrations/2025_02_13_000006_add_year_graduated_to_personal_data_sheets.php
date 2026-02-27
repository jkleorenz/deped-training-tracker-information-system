<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * PDS Section III: Year graduated for elementary, secondary, vocational, college, graduate studies.
     */
    public function up(): void
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $levels = ['elem', 'secondary', 'voc', 'college', 'grad'];
            foreach ($levels as $pre) {
                if (! Schema::hasColumn('personal_data_sheets', "{$pre}_year_graduated")) {
                    $table->string("{$pre}_year_graduated", 10)->nullable()->after("{$pre}_highest_level_units");
                }
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
                'elem_year_graduated',
                'secondary_year_graduated',
                'voc_year_graduated',
                'college_year_graduated',
                'grad_year_graduated',
            ]);
        });
    }
};
