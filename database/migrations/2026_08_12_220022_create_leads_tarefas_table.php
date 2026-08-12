<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads_tarefas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->dateTime('vencimento_em');
            $table->boolean('concluida')->default(false);
            $table->dateTime('concluida_em')->nullable();
            $table->timestamps();

            $table->index(['empresa_id', 'concluida', 'vencimento_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads_tarefas');
    }
};
