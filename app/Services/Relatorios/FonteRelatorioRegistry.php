<?php

namespace App\Services\Relatorios;

use App\Models\Cliente;
use App\Models\Lead;
use App\Models\Veiculo;
use App\Models\Venda;

/**
 * Registro das fontes de dado disponiveis pro construtor de relatorios
 * genericos (ver Livewire\Relatorios\Construtor). Cada fonte declara os
 * campos exibiveis, o campo de data usado pro filtro de periodo, um campo
 * numerico opcional pra soma no agrupamento, e quais campos podem ser
 * usados como "agrupar por". Adicionar uma fonte nova = so mexer aqui,
 * o componente e a exportacao ja funcionam pra qualquer entrada.
 */
class FonteRelatorioRegistry
{
    public static function fontes(): array
    {
        return [
            'veiculos' => [
                'label' => 'Veículos',
                'model' => Veiculo::class,
                'campos' => [
                    'marca' => 'Marca',
                    'modelo' => 'Modelo',
                    'versao' => 'Versão',
                    'ano_fabricacao' => 'Ano fabricação',
                    'km' => 'KM',
                    'cor' => 'Cor',
                    'combustivel' => 'Combustível',
                    'cambio' => 'Câmbio',
                    'status' => 'Status',
                    'preco_compra' => 'Preço de compra',
                    'preco_venda' => 'Preço de venda',
                    'created_at' => 'Data de cadastro',
                ],
                'campo_data' => 'created_at',
                'campo_valor' => 'preco_venda',
                'campos_agrupaveis' => ['status', 'marca', 'combustivel', 'cambio', 'cor'],
                'labels_valor' => [
                    'status' => Veiculo::STATUS_LABELS,
                ],
            ],

            'vendas' => [
                'label' => 'Vendas',
                'model' => Venda::class,
                'campos' => [
                    'forma_pagamento' => 'Forma de pagamento',
                    'preco_venda' => 'Valor de venda',
                    'desconto' => 'Desconto',
                    'comissao_vendedor' => 'Comissão',
                    'status' => 'Status',
                    'data_venda' => 'Data da venda',
                ],
                'campo_data' => 'data_venda',
                'campo_valor' => 'preco_venda',
                'campos_agrupaveis' => ['forma_pagamento', 'status'],
                'labels_valor' => [],
            ],

            'leads' => [
                'label' => 'Leads',
                'model' => Lead::class,
                'campos' => [
                    'nome' => 'Nome',
                    'telefone' => 'Telefone',
                    'origem' => 'Origem',
                    'portal_origem' => 'Portal de origem',
                    'estagio' => 'Estágio',
                    'lead_falso' => 'Lead falso?',
                    'created_at' => 'Data de entrada',
                ],
                'campo_data' => 'created_at',
                'campo_valor' => null,
                'campos_agrupaveis' => ['origem', 'portal_origem', 'estagio', 'lead_falso'],
                'labels_valor' => [
                    'estagio' => Lead::ESTAGIO_LABELS,
                ],
            ],

            'clientes' => [
                'label' => 'Clientes',
                'model' => Cliente::class,
                'campos' => [
                    'nome' => 'Nome',
                    'cpf' => 'CPF',
                    'telefone' => 'Telefone',
                    'email' => 'E-mail',
                    'created_at' => 'Data de cadastro',
                ],
                'campo_data' => 'created_at',
                'campo_valor' => null,
                'campos_agrupaveis' => [],
                'labels_valor' => [],
            ],
        ];
    }

    public static function fonte(string $chave): ?array
    {
        return self::fontes()[$chave] ?? null;
    }
}
