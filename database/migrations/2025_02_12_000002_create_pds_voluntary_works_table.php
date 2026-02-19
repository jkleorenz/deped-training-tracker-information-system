<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CS Form No. 212 Revised 2025 — Page 3, Section VI. Voluntary Work
     */
    public function up(): void
    {
        Schema::create('pds_voluntary_works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_data_sheet_id')->constrained()->cascadeOnDelete();
            $table->string('conducted_sponsored_by', 500)->nullable();
            $table->date('inclusive_dates_from')->nullable();
            $table->date('inclusive_dates_to')->nullable();
            $table->string('position_nature_of_work', 255)->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pds_voluntary_works');
    }
};
