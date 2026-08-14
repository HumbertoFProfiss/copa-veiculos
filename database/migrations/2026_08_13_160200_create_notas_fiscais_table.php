<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // NF-e simulada - emitir de verdade depende de certificado digital
        // A1/A3 e credenciamento na SEFAZ do estado (só o usuário pode
        // iniciar isso). Aqui geramos um documento ilustrativo com número e
        // chave de acesso fictícios, sempre marcado como simulação na UI/PDF.
        Schema::create('notas_fiscais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('venda_id')->constrained('vendas')->cascadeOnDelete();
            $table->foreignId('emitida_por')->nullable()->constrained('users')->nullOnDelete();

            $table->string('numero');
            $table->string('serie')->default('1');
            $table->string('chave_acesso', 44);
            $table->decimal('valor', 12, 2);
            $table->enum('status', ['emitida', 'cancelada'])->default('emitida');
            $table->timestamp('emitida_em');
            $table->timestamp('cancelada_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_fiscais');
    }
};
