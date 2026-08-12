<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carros_troca', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('venda_id')->constrained('vendas')->cascadeOnDelete();
            $table->string('marca');
            $table->string('modelo');
            $table->year('ano_modelo')->nullable();
            $table->string('placa', 8)->nullable();
            $table->unsignedInteger('km')->nullable();
            $table->decimal('valor_avaliado', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carros_troca');
    }
};
