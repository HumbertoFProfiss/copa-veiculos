<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Empresas ja existentes no plano completo_opcionais ganham uma
        // contagem de 7 dias a partir de agora - o plano deixou de custar
        // R$550/mes e virou periodo de teste (ver Empresa::PRECOS_PLANOS).
        DB::table('empresas')
            ->where('plano', 'completo_opcionais')
            ->whereNull('trial_termina_em')
            ->update(['trial_termina_em' => now()->addDays(7)]);
    }

    public function down(): void
    {
        // Irreversivel de forma segura - nao ha como distinguir quem ja
        // tinha trial_termina_em de quem foi preenchido por esta migration.
    }
};
