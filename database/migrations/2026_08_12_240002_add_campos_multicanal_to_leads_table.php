<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->enum('portal_origem', [
                'webmotors', 'icarros', 'chavesnamao', 'mobautos', 'napista',
                'carrossp', 'mercadolivre', 'facebook', 'site_proprio',
            ])->nullable()->after('origem');
            $table->text('mensagem_original')->nullable()->after('portal_origem');
            $table->boolean('lead_falso')->default(false)->after('estagio');
            $table->string('motivo_bloqueio')->nullable()->after('lead_falso');
            // Fica null até o primeiro contato ser registrado - é o que
            // alimenta o cronômetro de SLA de primeiro atendimento.
            $table->timestamp('respondido_em')->nullable()->after('motivo_bloqueio');
            $table->foreignId('contato_id')->nullable()->after('id')->constrained('contatos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contato_id');
            $table->dropColumn(['portal_origem', 'mensagem_original', 'lead_falso', 'motivo_bloqueio', 'respondido_em']);
        });
    }
};
