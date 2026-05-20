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
        Schema::create('certifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('certificate_template_id')->nullable();
            $table->uuid('category_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['Presencial', 'Semi-presencial', 'Ao Vivo', 'Online'])->default('Online');
            $table->integer('duration_hours')->nullable();
            $table->string('level')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('validity_months')->nullable();
            $table->enum('status', ['Ativo', 'Inativo', 'Rascunho'])->default('Ativo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
