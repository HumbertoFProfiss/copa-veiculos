<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Transferencia de propriedade no Renave simulada - registrar de
        // verdade depende de credenciamento no Registro Nacional de Veiculos
        // Automotores (DENATRAN), que so o usuario pode iniciar. Aqui geramos
        // um protocolo ilustrativo, sempre marcado como simulacao na UI/PDF.
        Schema::create('renave_transferencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('venda_id')->constrained('vendas')->cascadeOnDelete();
            $table->foreignId('gerada_por')->nullable()->constrained('users')->nullOnDelete();

            $table->string('protocolo', 20);
            $table->enum('status', ['concluida', 'cancelada'])->default('concluida');
            $table->timestamp('transferida_em');
            $table->timestamp('cancelada_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renave_transferencias');
    }
};
