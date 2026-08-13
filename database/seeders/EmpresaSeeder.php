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
                'telefone' => '1140028922',
                'whatsapp' => '11987654321',
                'email_contato' => 'contato@empresa-a-demo.test',
                'endereco' => 'Av. Exemplo, 1000 - Centro',
                'cidade' => 'São Paulo',
                'uf' => 'SP',
                'horario_funcionamento' => 'Seg-Sex: 08h às 18h30 | Sáb: 08h às 13h',
                'sobre_texto' => 'Empresa de demonstração usada para testar o painel multi-tenant. Substitua estes dados pelos reais em Configurações.',
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
