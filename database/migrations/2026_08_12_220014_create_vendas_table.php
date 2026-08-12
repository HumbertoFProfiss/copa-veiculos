<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('veiculo_id')->constrained('veiculos')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('vendedor_id')->constrained('users')->cascadeOnDelete();

            $table->enum('forma_pagamento', ['avista', 'financiado', 'consorcio', 'troca'])->default('avista');
            $table->decimal('preco_venda', 12, 2);
            $table->decimal('desconto', 12, 2)->default(0);
            $table->decimal('comissao_vendedor', 12, 2)->nullable();

            $table->enum('status', ['pendente', 'confirmada', 'cancelada'])->default('pendente');
            $table->date('data_venda');
            $table->date('data_entrega')->nullable();
            $table->unsignedSmallInteger('prazo_garantia_dias')->default(90);
            $table->string('numero_contrato')->nullable();

            $table->timestamps();

            $table->index(['empresa_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
