<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CS Form No. 212 — Section IV. Civil Service Eligibility
     */
    public function up(): void
    {
        Schema::create('civil_service_eligibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_data_sheet_id')->constrained()->cascadeOnDelete();
            $table->string('eligibility_type', 500)->nullable();
            $table->string('rating', 50)->nullable();
            $table->date('date_exam_conferment')->nullable();
            $table->string('place_exam_conferment', 255)->nullable();
            $table->string('license_number', 100)->nullable();
            $table->date('license_valid_until')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('civil_service_eligibilities');
    }
};
