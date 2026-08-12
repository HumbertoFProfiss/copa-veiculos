<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Seeder;

/**
 * Semeia empresas de teste - usadas no smoke test manual/automatizado de
 * isolamento multi-tenant (ver §12 do plano: nada feito na empresa-a pode
 * aparecer pra quem loga na empresa-b).
 */
class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        Empresa::query()->firstOrCreate(
            ['slug' => 'empresa-a'],
            [
                'nome' => 'Empresa A Demo Ltda',
                'plano' => 'completo_opcionais',
                'status' => 'ativo',
            ]
        );

        Empresa::query()->firstOrCreate(
            ['slug' => 'empresa-b'],
            [
                'nome' => 'Empresa B Demo Ltda',
                'plano' => 'completo',
                'status' => 'ativo',
            ]
        );
    }
}
