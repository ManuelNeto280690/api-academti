<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->string('subtitle')->nullable()->after('title');
            $table->json('prerequisites')->nullable()->after('description');
            $table->json('objectives')->nullable()->after('prerequisites');
            $table->json('exam_format')->nullable()->after('objectives');
            $table->json('salary_range')->nullable()->after('exam_format');
            $table->json('partner_companies')->nullable()->after('salary_range');
        });
    }

    public function down(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->dropColumn(['subtitle', 'prerequisites', 'objectives', 'exam_format', 'salary_range', 'partner_companies']);
        });
    }
};
