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
        Schema::create('class_users', function (Blueprint $table) {
            $table->uuid('class_id');
            $table->uuid('student_id');
            $table->timestamps();

            $table->primary(['class_id', 'student_id']);

            $table->foreign('class_id')->references('id')->on('classes');
            $table->foreign('student_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_users');
    }
};
