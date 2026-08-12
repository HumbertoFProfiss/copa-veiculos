<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ofx_lancamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('ofx_importacao_id')->constrained('ofx_importacoes')->cascadeOnDelete();
            $table->date('data');
            $table->string('descricao');
            $table->decimal('valor', 12, 2);
            $table->enum('tipo', ['credito', 'debito']);
            $table->boolean('conciliado')->default(false);
            $table->foreignId('contas_pagar_id')->nullable()->constrained('contas_pagar')->nullOnDelete();
            $table->foreignId('contas_receber_id')->nullable()->constrained('contas_receber')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ofx_lancamentos');
    }
};
