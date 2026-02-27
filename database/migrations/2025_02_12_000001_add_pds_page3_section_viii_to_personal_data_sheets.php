<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CS Form No. 212 Revised 2025 — Page 3, Section VIII. Other Information
     */
    public function up(): void
    {
        $addIfMissing = function (Blueprint $table, string $name) {
            if (! Schema::hasColumn('personal_data_sheets', $name)) {
                $table->text($name)->nullable();
            }
        };

        Schema::table('personal_data_sheets', function (Blueprint $table) use ($addIfMissing) {
            $addIfMissing($table, 'special_skills_hobbies');
            $addIfMissing($table, 'non_academic_distinctions');
            $addIfMissing($table, 'membership_in_associations');
        });
    }

    public function down(): void
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->dropColumn(['special_skills_hobbies', 'non_academic_distinctions', 'membership_in_associations']);
        });
    }
};
