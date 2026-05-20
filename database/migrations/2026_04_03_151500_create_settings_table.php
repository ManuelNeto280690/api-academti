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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed initial course rules
        DB::table('settings')->insert([
            ['key' => 'course_sequential_unlock', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'course_min_pass_score', 'value' => '70', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'course_certificate_enabled', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'course_access_duration_days', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
