<?php

namespace Database\Seeders;

use App\Models\CategoriaFinanceira;
use Illuminate\Database\Seeder;

/**
 * Plano de contas padrão pra empresa não começar com a tela de
 * Categorias financeiras vazia. Usa firstOrCreate por nome+tipo, então
 * reaproveita categorias já criadas ad-hoc (atalho de custos do veículo,
 * repasse de consignação) em vez de duplicar.
 */
class CategoriaFinanceiraSeeder extends Seeder
{
    public function run(): void
    {
        $custosVeiculo = CategoriaFinanceira::firstOrCreate(['nome' => 'Custos de veículo', 'tipo' => 'despesa']);
        foreach ([
            'Funilaria e pintura',
            'Mecânica e revisão',
            'Higienização e estética',
            'Documentação e transferência',
            'Pátio, guincho e transporte',
            'Peças e acessórios',
            'Fotos e anúncio',
        ] as $nome) {
            CategoriaFinanceira::firstOrCreate(
                ['nome' => $nome, 'tipo' => 'despesa'],
                ['categoria_pai_id' => $custosVeiculo->id]
            );
        }

        $despesasAdmin = CategoriaFinanceira::firstOrCreate(['nome' => 'Despesas administrativas', 'tipo' => 'despesa']);
        foreach ([
            'Aluguel',
            'Salários e comissões',
            'Marketing e anúncios',
            'Contabilidade',
            'Água, luz e internet',
        ] as $nome) {
            CategoriaFinanceira::firstOrCreate(
                ['nome' => $nome, 'tipo' => 'despesa'],
                ['categoria_pai_id' => $despesasAdmin->id]
            );
        }

        CategoriaFinanceira::firstOrCreate(['nome' => 'Repasse de consignação', 'tipo' => 'despesa']);

        foreach ([
            'Vendas de veículos',
            'Comissões recebidas',
            'Outras receitas',
        ] as $nome) {
            CategoriaFinanceira::firstOrCreate(['nome' => $nome, 'tipo' => 'receita']);
        }
    }
}
