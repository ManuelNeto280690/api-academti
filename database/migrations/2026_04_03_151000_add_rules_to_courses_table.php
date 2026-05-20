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
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('sequential_unlock')->default(false)->after('status');
            $table->integer('min_pass_score')->default(70)->after('sequential_unlock');
            $table->integer('access_duration_days')->nullable()->after('min_pass_score');
            $table->boolean('certificate_enabled')->default(true)->after('access_duration_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['sequential_unlock', 'min_pass_score', 'access_duration_days', 'certificate_enabled']);
        });
    }
};
