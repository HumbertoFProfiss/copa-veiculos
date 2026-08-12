<?php

namespace Database\Seeders;

use App\Models\ContratoModelo;
use App\Models\Empresa;
use Illuminate\Database\Seeder;

class ContratoModeloSeeder extends Seeder
{
    public function run(): void
    {
        Empresa::all()->each(function (Empresa $empresa) {
            ContratoModelo::firstOrCreate(
                ['empresa_id' => $empresa->id, 'tipo' => 'compra_venda'],
                [
                    'nome' => 'Contrato de Compra e Venda',
                    'corpo_html' => $this->compraVenda(),
                ]
            );

            ContratoModelo::firstOrCreate(
                ['empresa_id' => $empresa->id, 'tipo' => 'consignacao'],
                [
                    'nome' => 'Contrato de Consignação',
                    'corpo_html' => $this->consignacao(),
                ]
            );

            ContratoModelo::firstOrCreate(
                ['empresa_id' => $empresa->id, 'tipo' => 'procuracao'],
                [
                    'nome' => 'Procuração',
                    'corpo_html' => $this->procuracao(),
                ]
            );
        });
    }

    protected function compraVenda(): string
    {
        return <<<'HTML'
        <h2 style="text-align:center">CONTRATO DE COMPRA E VENDA DE VEÍCULO</h2>
        <p><strong>VENDEDOR:</strong> {{empresa.nome}}, CNPJ {{empresa.cnpj}}.</p>
        <p><strong>COMPRADOR:</strong> {{cliente.nome}}, CPF {{cliente.cpf}}, RG {{cliente.rg}}, residente em {{cliente.endereco}}.</p>
        <h3>DO OBJETO</h3>
        <p>O vendedor vende ao comprador o veículo {{veiculo.marca}} {{veiculo.modelo}} {{veiculo.versao}}, ano {{veiculo.ano}},
        cor {{veiculo.cor}}, placa {{veiculo.placa}}, chassi {{veiculo.chassi}}, renavam {{veiculo.renavam}}, com {{veiculo.km}} km rodados.</p>
        <h3>DO PREÇO</h3>
        <p>O valor total da venda é de {{valor.total}} ({{valor.extenso}}), pagos {{venda.forma_pagamento}}.</p>
        <h3>DA GARANTIA</h3>
        <p>O veículo é vendido com garantia de {{venda.garantia_dias}} dias, contados a partir de {{venda.data}}.</p>
        <p>{{data.hoje}}.</p>
        <br><br>
        <p>_________________________________<br>{{empresa.nome}}</p>
        <br>
        <p>_________________________________<br>{{cliente.nome}}</p>
        HTML;
    }

    protected function consignacao(): string
    {
        return <<<'HTML'
        <h2 style="text-align:center">CONTRATO DE CONSIGNAÇÃO DE VEÍCULO</h2>
        <p><strong>CONSIGNATÁRIA:</strong> {{empresa.nome}}, CNPJ {{empresa.cnpj}}.</p>
        <p><strong>CONSIGNANTE:</strong> {{cliente.nome}}, CPF {{cliente.cpf}}, residente em {{cliente.endereco}}.</p>
        <h3>DO OBJETO</h3>
        <p>O consignante entrega à consignatária, para fins de venda, o veículo {{veiculo.marca}} {{veiculo.modelo}}
        {{veiculo.versao}}, ano {{veiculo.ano}}, placa {{veiculo.placa}}, chassi {{veiculo.chassi}}.</p>
        <h3>DO VALOR</h3>
        <p>O valor mínimo de venda acordado é de {{valor.total}} ({{valor.extenso}}).</p>
        <p>{{data.hoje}}.</p>
        <br><br>
        <p>_________________________________<br>{{empresa.nome}}</p>
        <br>
        <p>_________________________________<br>{{cliente.nome}}</p>
        HTML;
    }

    protected function procuracao(): string
    {
        return <<<'HTML'
        <h2 style="text-align:center">PROCURAÇÃO</h2>
        <p><strong>OUTORGANTE:</strong> {{cliente.nome}}, CPF {{cliente.cpf}}, residente em {{cliente.endereco}}.</p>
        <p><strong>OUTORGADA:</strong> {{empresa.nome}}, CNPJ {{empresa.cnpj}}.</p>
        <p>Pelo presente instrumento, o outorgante nomeia e constitui a outorgada como sua procuradora, para o fim
        específico de representá-lo perante o DETRAN e demais órgãos competentes, para tratar de assuntos relativos
        ao veículo {{veiculo.marca}} {{veiculo.modelo}}, placa {{veiculo.placa}}, chassi {{veiculo.chassi}},
        podendo transferir a propriedade e praticar todos os atos necessários ao fiel cumprimento deste mandato.</p>
        <p>{{data.hoje}}.</p>
        <br><br>
        <p>_________________________________<br>{{cliente.nome}}</p>
        HTML;
    }
}
