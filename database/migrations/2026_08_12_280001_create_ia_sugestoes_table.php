<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ia_sugestoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->enum('tipo', ['preco', 'descricao', 'resposta_crm', 'insight_estoque']);
            // Polimórfico: sugestão pode ser sobre um Veiculo, um Lead, etc.
            $table->nullableMorphs('sugerivel');
            $table->text('conteudo_sugerido');
            $table->json('contexto_usado')->nullable();
            $table->enum('status', ['pendente', 'aceita', 'editada', 'descartada'])->default('pendente');
            $table->foreignId('usuario_decisao_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ia_sugestoes');
    }
};
