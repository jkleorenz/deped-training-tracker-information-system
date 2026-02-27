<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add specify field for when Type of L&D is "Other"
     */
    public function up(): void
    {
        Schema::table('pds_learning_developments', function (Blueprint $table) {
            if (! Schema::hasColumn('pds_learning_developments', 'type_of_ld_specify')) {
                $table->string('type_of_ld_specify', 100)->nullable()->after('type_of_ld');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pds_learning_developments', function (Blueprint $table) {
            $table->dropColumn('type_of_ld_specify');
        });
    }
};
