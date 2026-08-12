<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcelas_financiamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('venda_id')->constrained('vendas')->cascadeOnDelete();
            $table->unsignedSmallInteger('numero_parcela');
            $table->decimal('valor', 12, 2);
            $table->date('vencimento');
            $table->date('pagamento')->nullable();
            $table->enum('status', ['pendente', 'paga', 'atrasada'])->default('pendente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcelas_financiamento');
    }
};
