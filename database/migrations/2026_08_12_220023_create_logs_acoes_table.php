<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs_acoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('acao');
            $table->string('tabela_afetada')->nullable();
            $table->unsignedBigInteger('registro_id')->nullable();
            $table->json('detalhes')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['empresa_id', 'tabela_afetada', 'registro_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_acoes');
    }
};
