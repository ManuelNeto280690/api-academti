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
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('trainer_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // New Attributes
            $table->enum('modalidade', ['presencial', 'semi-presencial', 'ao-vivo', 'online'])->default('online');
            $table->decimal('preco_normal', 10, 2)->default(0);
            $table->decimal('preco_promocional', 10, 2)->nullable();
            $table->string('imagem')->nullable();
            $table->string('url_video_destaque')->nullable();
            $table->json('tags')->nullable();
            $table->integer('numero_alunos')->default(0);
            $table->integer('pagamento_vezes')->default(1);
            $table->enum('tipo_destaque', ['novo', 'destaque', 'Bestseller', 'popular'])->nullable();
            $table->enum('tipo_acesso', ['interno', 'externo'])->default('interno');
            
            // Live Session Fields
            $table->enum('meeting_platform', ['zoom', 'teams', 'meet', 'outro'])->nullable();
            $table->string('meeting_link', 500)->nullable();
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();
            
            $table->decimal('rating', 3, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
