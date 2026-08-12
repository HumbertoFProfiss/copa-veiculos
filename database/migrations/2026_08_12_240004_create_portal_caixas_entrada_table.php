<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_caixas_entrada', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->enum('portal', [
                'webmotors', 'icarros', 'chavesnamao', 'mobautos', 'napista',
                'carrossp', 'mercadolivre', 'facebook',
            ]);
            $table->string('email_dedicado')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->unique(['empresa_id', 'portal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_caixas_entrada');
    }
};
