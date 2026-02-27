<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CS Form No. 212 Revised 2025 — Page 3, Section VII. L&D Interventions/Training Programs
     */
    public function up(): void
    {
        Schema::create('pds_learning_developments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_data_sheet_id')->constrained()->cascadeOnDelete();
            $table->string('organization_name_address', 500)->nullable(); // 29. Name & Address of Organization
            $table->string('title_of_ld', 500)->nullable(); // 30. Title of L&D
            $table->string('type_of_ld', 100)->nullable(); // Managerial/Supervisory/Technical/etc
            $table->integer('number_of_hours')->nullable();
            $table->date('inclusive_dates_from')->nullable();
            $table->date('inclusive_dates_to')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pds_learning_developments');
    }
};
