<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores')->nullOnDelete();

            $table->string('marca');
            $table->string('modelo');
            $table->string('versao')->nullable();
            $table->string('slug');
            $table->year('ano_fabricacao')->nullable();
            $table->year('ano_modelo')->nullable();
            $table->unsignedInteger('km')->default(0);
            $table->string('combustivel')->nullable();
            $table->string('cambio')->nullable();
            $table->string('cor')->nullable();
            $table->unsignedTinyInteger('portas')->nullable();
            $table->string('motor')->nullable();

            $table->string('placa', 8)->nullable();
            $table->string('numero_chassi', 30)->nullable();
            $table->string('renavam', 20)->nullable();
            $table->string('numero_estoque')->nullable();

            $table->enum('tipo_propriedade', ['proprio', 'consignado', 'terceiro'])->default('proprio');
            $table->string('consignado_nome')->nullable();
            $table->string('consignado_telefone', 20)->nullable();

            $table->decimal('preco_compra', 12, 2)->nullable();
            $table->decimal('preco_tabela_fipe', 12, 2)->nullable();
            $table->decimal('preco_anuncio', 12, 2)->nullable();
            $table->decimal('preco_venda', 12, 2)->nullable();
            $table->decimal('preco_minimo', 12, 2)->nullable();

            $table->string('situacao_documental')->nullable();
            $table->boolean('chave_reserva')->default(false);
            $table->boolean('manual')->default(false);
            $table->string('estado_conservacao')->nullable();
            $table->boolean('gravame')->default(false);
            $table->decimal('debitos', 12, 2)->nullable();
            $table->boolean('ipva_pago')->default(false);
            $table->boolean('licenciado')->default(false);

            $table->date('data_entrada')->nullable();
            $table->string('local_patio')->nullable();

            $table->enum('status', ['em_preparacao', 'disponivel', 'reservado', 'vendido', 'entregue', 'devolvido'])
                ->default('em_preparacao');
            $table->boolean('destaque')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['empresa_id', 'slug']);
            $table->index(['empresa_id', 'placa']);
            $table->index(['empresa_id', 'numero_chassi']);
            $table->index(['empresa_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};
