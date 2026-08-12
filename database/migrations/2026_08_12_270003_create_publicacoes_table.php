<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publicacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('veiculo_id')->constrained('veiculos')->cascadeOnDelete();
            $table->foreignId('canal_id')->constrained('canais')->cascadeOnDelete();

            $table->enum('status', ['pendente', 'publicado', 'erro', 'despublicado'])->default('pendente');
            $table->string('external_id')->nullable();
            $table->string('url_anuncio')->nullable();
            $table->timestamp('ultima_sincronizacao_em')->nullable();
            $table->text('ultimo_erro')->nullable();
            $table->json('payload_enviado')->nullable();
            $table->unsignedInteger('tentativas')->default(0);

            $table->timestamps();

            $table->unique(['veiculo_id', 'canal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publicacoes');
    }
};
