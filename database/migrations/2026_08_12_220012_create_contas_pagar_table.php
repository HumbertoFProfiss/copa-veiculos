<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contas_pagar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_financeiras')->nullOnDelete();
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->nullOnDelete();
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores')->nullOnDelete();
            $table->string('descricao');
            $table->decimal('valor', 12, 2);
            $table->date('vencimento');
            $table->date('pagamento')->nullable();
            $table->enum('status', ['pendente', 'pago', 'cancelado', 'atrasado'])->default('pendente');
            $table->enum('recorrencia', ['nenhuma', 'semanal', 'mensal', 'anual'])->default('nenhuma');
            $table->timestamps();

            $table->index(['empresa_id', 'status', 'vencimento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contas_pagar');
    }
};
