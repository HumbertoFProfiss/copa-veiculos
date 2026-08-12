<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('importacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('origem', ['boom', 'csv_generico', 'xlsx_generico', 'xml_generico'])->default('csv_generico');
            $table->string('nome_arquivo_original')->nullable();
            $table->string('arquivo_path')->nullable();
            $table->enum('status', ['mapeando', 'validando', 'importando', 'concluido', 'erro'])->default('mapeando');

            $table->unsignedInteger('total_linhas')->default(0);
            $table->unsignedInteger('total_importados')->default(0);
            $table->unsignedInteger('total_duplicados')->default(0);
            $table->unsignedInteger('total_erros')->default(0);

            $table->json('mapeamento_usado')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('importacoes');
    }
};
