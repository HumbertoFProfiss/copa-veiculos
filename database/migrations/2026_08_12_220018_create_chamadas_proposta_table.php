<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chamadas_proposta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('veiculo_id')->nullable()->constrained('veiculos')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('tipo', ['ligacao', 'whatsapp', 'presencial', 'email']);
            $table->enum('intencao', ['comprar', 'vender', 'consignar']);
            $table->enum('resultado', ['sem_resposta', 'em_negociacao', 'fechado', 'perdido'])->default('sem_resposta');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chamadas_proposta');
    }
};
