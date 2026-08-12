<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contas_receber', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_financeiras')->nullOnDelete();
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            // FK real pra vendas é adicionada em 2026_08_12_220018_add_venda_id_fk_to_contas_receber_table
            // (a tabela vendas só é criada depois, na ordem do plano - ver §3.1).
            $table->unsignedBigInteger('venda_id')->nullable();
            $table->string('descricao');
            $table->decimal('valor', 12, 2);
            $table->date('vencimento');
            $table->date('pagamento')->nullable();
            $table->enum('status', ['pendente', 'recebido', 'cancelado', 'atrasado'])->default('pendente');
            $table->enum('recorrencia', ['nenhuma', 'semanal', 'mensal', 'anual'])->default('nenhuma');
            $table->timestamps();

            $table->index(['empresa_id', 'status', 'vencimento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contas_receber');
    }
};
