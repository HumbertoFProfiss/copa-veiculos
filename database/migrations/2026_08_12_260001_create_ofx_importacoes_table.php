<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ofx_importacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('arquivo_path')->nullable();
            $table->string('banco_nome')->nullable();
            $table->unsignedInteger('total_lancamentos')->default(0);
            $table->unsignedInteger('total_conciliados')->default(0);
            $table->enum('status', ['processando', 'concluido', 'erro'])->default('processando');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ofx_importacoes');
    }
};
