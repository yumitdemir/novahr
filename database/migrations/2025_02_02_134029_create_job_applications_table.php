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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ["pending","accepted","rejected"]);
            $table->date('application_date')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('surname', 100)->nullable();
            $table->text('cv')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('location')->nullable();
            $table->string('current_job_title')->nullable();
            $table->string('current_employer')->nullable();
            $table->integer('years_of_experience')->nullable();
            $table->string('university')->nullable();
            $table->string('certifications')->nullable();
            $table->string('technical_skills')->nullable();
            $table->string('soft_skills')->nullable();
            $table->string('languages_spoken')->nullable();
            $table->string('compatibility_rating')->nullable();
            $table->foreignId('job_opening_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
