<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add Type of L&D (Managerial/Supervisory/Technical/Other) for PDS alignment.
     */
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->string('type_of_ld', 100)->nullable()->after('type');
            $table->string('type_of_ld_specify', 100)->nullable()->after('type_of_ld');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn(['type_of_ld', 'type_of_ld_specify']);
        });
    }
};
