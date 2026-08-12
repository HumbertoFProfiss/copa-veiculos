<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('veiculos_opcionais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('veiculo_id')->constrained('veiculos')->cascadeOnDelete();
            $table->string('nome');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('veiculos_opcionais');
    }
};
