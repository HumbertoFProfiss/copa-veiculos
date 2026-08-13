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
                'nome' => 'Copa Veículos',
                'plano' => 'completo_opcionais',
                'status' => 'ativo',
                'telefone' => '1438825011',
                'whatsapp' => '14997542803',
                'email_contato' => 'copa@yahoo.com.br',
                'endereco' => 'Av. Dr. Vital Brasil, 580 - Vila São Lúcio - CEP 18603-193',
                'cidade' => 'Botucatu',
                'uf' => 'SP',
                'horario_funcionamento' => 'Seg-Sex: 08h às 18h30 | Sáb: 08h às 13h',
                'sobre_texto' => 'Estamos no mercado de trabalho desde 2006. Somos uma empresa familiar e nosso lema é confiança e credibilidade. Buscamos os melhores veículos do mercado para atender nossos clientes, com atendimento diferenciado, explicando todos os detalhes do veículo e esclarecendo todas as dúvidas.',
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
