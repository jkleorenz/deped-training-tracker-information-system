<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('personnel')->after('email'); // admin | personnel
            $table->string('employee_id', 50)->nullable()->after('role');
            $table->string('designation', 100)->nullable()->after('employee_id');
            $table->string('department', 100)->nullable()->after('designation');
            $table->string('school', 255)->nullable()->after('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'employee_id', 'designation', 'department', 'school']);
        });
    }
};
