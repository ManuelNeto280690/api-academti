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
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignUuid('course_id')->nullable()->change();
            $table->foreignUuid('certification_id')->nullable()->after('course_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignUuid('course_id')->nullable(false)->change();
            $table->dropForeign(['certification_id']);
            $table->dropColumn('certification_id');
        });
    }
};
