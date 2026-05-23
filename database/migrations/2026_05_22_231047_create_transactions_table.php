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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // Curso, Certificação, Mentoria, Subscrição, etc
            $table->uuid('item_id')->nullable(); // ID do curso/certificação
            $table->decimal('amount', 10, 2); // Valor total pago
            $table->string('payment_method')->nullable();
            
            $table->foreignUuid('trainer_id')->nullable()->constrained('users')->onDelete('set null'); // O formador (se aplicável)
            $table->decimal('trainer_amount', 10, 2)->default(0); // Comissão do formador
            $table->decimal('platform_amount', 10, 2)->default(0); // Valor retido pela plataforma
            
            $table->string('status')->default('concluído'); // concluído, pendente, reembolsado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
