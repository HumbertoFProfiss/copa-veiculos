<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente_favoritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('veiculo_id')->constrained('veiculos')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['cliente_id', 'veiculo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_favoritos');
    }
};
