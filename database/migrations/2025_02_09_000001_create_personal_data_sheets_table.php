<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CS Form No. 212, Revised 2025 — Section I. Personal Information
     */
    public function up(): void
    {
        Schema::create('personal_data_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 1–2. Name
            $table->string('surname', 100)->nullable();
            $table->string('name_extension', 20)->nullable(); // JR., SR
            $table->string('first_name', 100)->nullable();
            $table->string('middle_name', 100)->nullable();

            // 3–4. Birth
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth', 255)->nullable();

            // 5. Sex at birth
            $table->string('sex', 10)->nullable(); // male, female

            // 6. Civil status
            $table->string('civil_status', 20)->nullable(); // single, married, widowed, separated, other
            $table->string('civil_status_other', 50)->nullable();

            // 7–9. Physical / Blood
            $table->decimal('height', 5, 2)->nullable(); // meters
            $table->decimal('weight', 5, 2)->nullable(); // kg
            $table->string('blood_type', 10)->nullable();

            // 10–15. IDs
            $table->string('umid_id', 50)->nullable();
            $table->string('pagibig_id', 50)->nullable();
            $table->string('philhealth_no', 50)->nullable();
            $table->string('philsys_number', 50)->nullable();
            $table->string('tin_no', 50)->nullable();
            $table->string('agency_employee_no', 50)->nullable();

            // 16. Citizenship
            $table->string('citizenship', 20)->nullable(); // filipino, dual
            $table->string('dual_citizenship_type', 20)->nullable(); // by_birth, by_naturalization
            $table->string('dual_citizenship_country', 100)->nullable();

            // 17. Residential address
            $table->string('residential_house_no', 100)->nullable();
            $table->string('residential_street', 255)->nullable();
            $table->string('residential_subdivision', 255)->nullable();
            $table->string('residential_barangay', 100)->nullable();
            $table->string('residential_city', 100)->nullable();
            $table->string('residential_province', 100)->nullable();
            $table->string('residential_zip', 20)->nullable();

            // 18. Permanent address
            $table->string('permanent_house_no', 100)->nullable();
            $table->string('permanent_street', 255)->nullable();
            $table->string('permanent_subdivision', 255)->nullable();
            $table->string('permanent_barangay', 100)->nullable();
            $table->string('permanent_city', 100)->nullable();
            $table->string('permanent_province', 100)->nullable();
            $table->string('permanent_zip', 20)->nullable();

            // 19–21. Contact
            $table->string('telephone', 50)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('email_address', 255)->nullable(); // PDS field; can mirror user.email

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_data_sheets');
    }
};
