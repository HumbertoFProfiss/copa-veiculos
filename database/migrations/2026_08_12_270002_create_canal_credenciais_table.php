<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('canal_credenciais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('canal_id')->constrained('canais')->cascadeOnDelete();
            $table->string('chave');
            // Credencial de verdade (token OAuth etc) - criptografada em
            // repouso, nunca em texto puro no banco.
            $table->text('valor');
            $table->timestamps();

            $table->unique(['empresa_id', 'canal_id', 'chave']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('canal_credenciais');
    }
};
