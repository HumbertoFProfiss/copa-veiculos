<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bloqueios_lead', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->enum('tipo', ['telefone', 'email', 'padrao']);
            $table->string('valor');
            $table->string('motivo')->nullable();
            $table->timestamps();

            $table->unique(['empresa_id', 'tipo', 'valor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bloqueios_lead');
    }
};
