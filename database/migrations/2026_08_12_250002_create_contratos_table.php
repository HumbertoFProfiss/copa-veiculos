<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('contrato_modelo_id')->constrained('contrato_modelos')->restrictOnDelete();
            $table->foreignId('venda_id')->nullable()->constrained('vendas')->nullOnDelete();
            $table->foreignId('veiculo_id')->nullable()->constrained('veiculos')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('criado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->string('numero')->unique();
            $table->enum('status', ['rascunho', 'enviado', 'assinado', 'cancelado'])->default('rascunho');
            // Snapshot imutável do HTML já com as variáveis preenchidas no
            // momento da geração - versionamento por congelamento, não edita
            // depois (se o modelo mudar, contratos antigos continuam intactos).
            $table->longText('corpo_html_gerado');
            $table->string('pdf_path')->nullable();

            // Assinatura eletrônica real depende de conta num provedor
            // (ClickSign/D4Sign/etc) que só existe quando o usuário contratar -
            // ver plano §Contexto. Esses campos ficam prontos pra isso.
            $table->string('assinatura_provider')->nullable();
            $table->enum('assinatura_status', ['nao_iniciada', 'pendente', 'assinado', 'recusado'])->default('nao_iniciada');
            $table->json('assinatura_metadata')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
