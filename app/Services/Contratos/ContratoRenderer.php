<?php

namespace App\Services\Contratos;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Venda;
use Carbon\Carbon;

/**
 * Motor de interpolação {{variavel}} - substitui {{cliente.nome}},
 * {{veiculo.placa}}, {{valor.total}} etc pelo dado real da venda/cliente/
 * veículo, incluindo o valor por extenso (ver NumeroPorExtenso). Porta a
 * lógica que existia em admin/contratos/gerar.php no app antigo.
 */
class ContratoRenderer
{
    public function renderizar(string $corpoHtml, Venda $venda, Empresa $empresa): string
    {
        $veiculo = $venda->veiculo;
        $cliente = $venda->cliente;
        $vendedor = $venda->vendedor;
        $valorTotal = (float) $venda->preco_venda - (float) $venda->desconto;

        $variaveis = [
            'empresa.nome' => $empresa->nome,
            'empresa.cnpj' => $empresa->cnpj ?? '',

            'cliente.nome' => $cliente->nome,
            'cliente.cpf' => $cliente->cpf ?? '',
            'cliente.rg' => $cliente->rg ?? '',
            'cliente.endereco' => $this->enderecoCompleto($cliente),
            'cliente.telefone' => $cliente->telefone ?? '',

            'veiculo.marca' => $veiculo->marca,
            'veiculo.modelo' => $veiculo->modelo,
            'veiculo.versao' => $veiculo->versao ?? '',
            'veiculo.ano' => "{$veiculo->ano_fabricacao}/{$veiculo->ano_modelo}",
            'veiculo.cor' => $veiculo->cor ?? '',
            'veiculo.placa' => $veiculo->placa ?? '',
            'veiculo.chassi' => $veiculo->numero_chassi ?? '',
            'veiculo.renavam' => $veiculo->renavam ?? '',
            'veiculo.km' => number_format($veiculo->km, 0, ',', '.'),

            'valor.total' => 'R$ '.number_format($valorTotal, 2, ',', '.'),
            'valor.extenso' => ucfirst((new NumeroPorExtenso)->moeda($valorTotal)),
            'valor.desconto' => 'R$ '.number_format((float) $venda->desconto, 2, ',', '.'),

            'venda.forma_pagamento' => $this->formaPagamentoLabel($venda->forma_pagamento),
            'venda.data' => $venda->data_venda->format('d/m/Y'),
            'venda.garantia_dias' => (string) $venda->prazo_garantia_dias,

            'vendedor.nome' => $vendedor->name ?? '',

            'data.hoje' => Carbon::now()->translatedFormat('d \d\e F \d\e Y'),
            'data.hoje_extenso' => Carbon::now()->translatedFormat('d \d\e F \d\e Y'),
        ];

        return preg_replace_callback('/\{\{\s*([a-z0-9_.]+)\s*\}\}/i', function ($match) use ($variaveis) {
            return $variaveis[$match[1]] ?? $match[0];
        }, $corpoHtml);
    }

    protected function enderecoCompleto(Cliente $cliente): string
    {
        return trim(implode(', ', array_filter([
            $cliente->endereco,
            $cliente->cidade,
            $cliente->uf,
            $cliente->cep,
        ])));
    }

    protected function formaPagamentoLabel(string $forma): string
    {
        return match ($forma) {
            'avista' => 'à vista',
            'financiado' => 'financiado',
            'consorcio' => 'consórcio',
            'troca' => 'troca com veículo',
            default => $forma,
        };
    }
}
