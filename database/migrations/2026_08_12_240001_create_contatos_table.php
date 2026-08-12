<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contatos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->string('nome');
            // Normalizado (só dígitos, sem DDI/zero à esquerda) pra dedup
            // funcionar de verdade - ver App\Services\Leads\LeadDeduplicator.
            $table->string('telefone_normalizado', 20)->nullable();
            $table->string('email_normalizado')->nullable();
            $table->timestamps();

            $table->index(['empresa_id', 'telefone_normalizado']);
            $table->index(['empresa_id', 'email_normalizado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contatos');
    }
};
