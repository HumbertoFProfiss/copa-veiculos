<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_origens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('contato_id')->constrained('contatos')->cascadeOnDelete();
            $table->enum('portal', [
                'webmotors', 'icarros', 'chavesnamao', 'mobautos', 'napista',
                'carrossp', 'mercadolivre', 'facebook', 'site_proprio',
            ]);
            $table->timestamp('recebido_em');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_origens');
    }
};
