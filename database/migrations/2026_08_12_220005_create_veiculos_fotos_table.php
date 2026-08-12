<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('veiculos_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('veiculo_id')->constrained('veiculos')->cascadeOnDelete();
            $table->string('path');
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->boolean('principal')->default(false);
            $table->timestamps();

            $table->index(['veiculo_id', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('veiculos_fotos');
    }
};
