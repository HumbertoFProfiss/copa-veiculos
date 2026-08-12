<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mapeamentos_importacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->string('nome');
            $table->enum('origem', ['boom', 'csv_generico', 'xlsx_generico', 'xml_generico']);
            // {"coluna_origem": "campo_destino"} - reaproveitado na próxima importação
            // da mesma origem, pra não repetir o mapeamento manual.
            $table->json('mapeamento');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mapeamentos_importacao');
    }
};
