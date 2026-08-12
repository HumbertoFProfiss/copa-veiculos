<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custos_veiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('veiculo_id')->constrained('veiculos')->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_financeiras')->nullOnDelete();
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores')->nullOnDelete();
            $table->string('descricao');
            $table->decimal('valor', 12, 2);
            $table->date('data')->nullable();
            $table->string('numero_nf')->nullable();
            $table->string('arquivo_nf')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custos_veiculo');
    }
};
