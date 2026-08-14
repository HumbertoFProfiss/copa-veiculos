<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // "MultiBanco" simulado - sem parceria/API bancária real (depende de
        // credenciamento que só o usuário pode iniciar). O cálculo de parcela
        // é real (tabela Price), mas a aprovação é decidida manualmente aqui
        // dentro, nunca por um banco de verdade. Ver aviso de simulação na UI.
        Schema::create('propostas_financiamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('venda_id')->constrained('vendas')->cascadeOnDelete();
            $table->foreignId('banco_id')->constrained('bancos')->restrictOnDelete();
            $table->foreignId('criado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->decimal('valor_financiado', 12, 2);
            $table->decimal('entrada', 12, 2)->default(0);
            $table->unsignedInteger('num_parcelas');
            $table->decimal('taxa_juros_am', 5, 2);
            $table->decimal('valor_parcela', 12, 2);
            $table->enum('status', ['simulada', 'aprovada', 'recusada'])->default('simulada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propostas_financiamento');
    }
};
