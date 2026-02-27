<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add NUMBER OF HOURS to Section VI. Voluntary Work
     */
    public function up(): void
    {
        Schema::table('pds_voluntary_works', function (Blueprint $table) {
            if (! Schema::hasColumn('pds_voluntary_works', 'number_of_hours')) {
                $table->integer('number_of_hours')->nullable()->after('position_nature_of_work');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pds_voluntary_works', function (Blueprint $table) {
            $table->dropColumn('number_of_hours');
        });
    }
};
