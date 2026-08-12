<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->string('nome');
            $table->string('cpf', 14)->nullable();
            $table->string('rg', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('endereco')->nullable();
            $table->string('cidade')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('profissao')->nullable();
            $table->decimal('renda_estimada', 12, 2)->nullable();
            // Coluna de credencial em qualquer tabela autenticável sempre se chama
            // "password" (mesma regra aplicada em users) - nullable pq nem todo
            // cliente cria login na área do cliente.
            $table->string('password')->nullable();
            $table->timestamps();

            $table->unique(['empresa_id', 'cpf']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
