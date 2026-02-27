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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type', 50)->nullable(); // seminar, training, workshop, etc.
            $table->string('provider', 255)->nullable();
            $table->string('venue', 255)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('hours')->nullable();
            $table->text('description')->nullable();
            $table->string('certificate_number', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
