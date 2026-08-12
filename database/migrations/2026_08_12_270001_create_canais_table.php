<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catálogo global (não é por empresa) - os 9 canais são os mesmos
        // pra qualquer revenda, só a credencial/publicação é que é por
        // empresa (ver canal_credenciais/publicacoes).
        Schema::create('canais', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('nome');
            $table->enum('tipo', ['csv', 'xml', 'api']);
            $table->boolean('ativo')->default(true);
            $table->json('config')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('canais');
    }
};
