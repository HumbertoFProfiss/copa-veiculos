<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('veiculo_id')->nullable()->constrained('veiculos')->nullOnDelete();
            $table->foreignId('vendedor_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('nome');
            $table->string('email')->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();

            $table->enum('origem', ['estoque', 'formulario_interesse', 'simulador', 'site', 'outro'])->default('site');
            // Substitui o antigo tipo_lead - estágios do funil kanban (ver plano §4.7).
            $table->enum('estagio', ['novo', 'em_atendimento', 'proposta', 'negociacao', 'ganho', 'perdido'])->default('novo');

            $table->string('ip_origem', 45)->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();

            $table->index(['empresa_id', 'estagio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
