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
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('category')->default('Geral'); // React, Python, Dúvidas, etc.
            $table->enum('status', ['ativo', 'oculto', 'banido'])->default('ativo');
            $table->boolean('pinned')->default(false);
            $table->integer('reported_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_posts');
    }
};
