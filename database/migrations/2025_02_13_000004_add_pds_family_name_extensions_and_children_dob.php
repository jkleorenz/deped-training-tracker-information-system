<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * PDS Section II: spouse name extension (22), children date of birth (23), father/mother name extension (24, 25).
     */
    public function up(): void
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            if (! Schema::hasColumn('personal_data_sheets', 'spouse_name_extension')) {
                $table->string('spouse_name_extension', 20)->nullable()->after('spouse_middle_name');
            }
            if (! Schema::hasColumn('personal_data_sheets', 'children_data')) {
                $table->json('children_data')->nullable()->after('children_names');
            }
            if (! Schema::hasColumn('personal_data_sheets', 'father_name_extension')) {
                $table->string('father_name_extension', 20)->nullable()->after('father_middle_name');
            }
            if (! Schema::hasColumn('personal_data_sheets', 'mother_name_extension')) {
                $table->string('mother_name_extension', 20)->nullable()->after('mother_middle_name');
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
                'spouse_name_extension',
                'children_data',
                'father_name_extension',
                'mother_name_extension',
            ]);
        });
    }
};
