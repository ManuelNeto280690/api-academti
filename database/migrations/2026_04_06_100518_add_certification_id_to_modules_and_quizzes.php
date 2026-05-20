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
        Schema::table('modules', function (Blueprint $table) {
            $table->uuid('certification_id')->nullable()->after('course_id');
            $table->foreign('certification_id')->references('id')->on('certifications')->onDelete('cascade');
            
            // Make course_id nullable if it wasn't already (usually it is for certifications)
            $table->uuid('course_id')->nullable()->change();
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->uuid('certification_id')->nullable()->after('course_id');
            $table->foreign('certification_id')->references('id')->on('certifications')->onDelete('cascade');
            
            // Make course_id nullable
            $table->uuid('course_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropForeign(['certification_id']);
            $table->dropColumn('certification_id');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['certification_id']);
            $table->dropColumn('certification_id');
        });
    }
};
