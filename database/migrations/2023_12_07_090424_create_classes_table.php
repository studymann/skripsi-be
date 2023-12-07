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
        Schema::create('classes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('package_id');
            $table->uuid('level_id');
            $table->uuid('semester_id');
            $table->string('year');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('package_id')->references('id')->on('packages');
            $table->foreign('level_id')->references('id')->on('levels');
            $table->foreign('semester_id')->references('id')->on('semesters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
