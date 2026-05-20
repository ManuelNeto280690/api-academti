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
        Schema::create('quiz_user', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $blueprint->foreignUuid('quiz_id')->constrained()->cascadeOnDelete();
            $blueprint->integer('score')->default(0);
            $blueprint->string('status')->default('completed');
            $blueprint->boolean('is_locked')->default(true);
            $blueprint->timestamp('completed_at')->nullable();
            $blueprint->timestamps();

            $blueprint->unique(['user_id', 'quiz_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_user');
    }
};
