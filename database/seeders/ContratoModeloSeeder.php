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

            ContratoModelo::firstOrCreate(
                ['empresa_id' => $empresa->id, 'tipo' => 'financiamento'],
                [
                    'nome' => 'Termo de Interveniência (Financiamento)',
                    'corpo_html' => $this->financiamento(),
                ]
            );

            ContratoModelo::firstOrCreate(
                ['empresa_id' => $empresa->id, 'tipo' => 'troca'],
                [
                    'nome' => 'Termo de Troca / Permuta de Veículo',
                    'corpo_html' => $this->troca(),
                ]
            );

            ContratoModelo::firstOrCreate(
                ['empresa_id' => $empresa->id, 'tipo' => 'garantia_estendida'],
                [
                    'nome' => 'Termo de Garantia Estendida',
                    'corpo_html' => $this->garantiaEstendida(),
                ]
            );

            ContratoModelo::firstOrCreate(
                ['empresa_id' => $empresa->id, 'tipo' => 'recibo_sinal'],
                [
                    'nome' => 'Recibo de Sinal',
                    'corpo_html' => $this->reciboSinal(),
                ]
            );

            ContratoModelo::firstOrCreate(
                ['empresa_id' => $empresa->id, 'tipo' => 'termo_entrega'],
                [
                    'nome' => 'Termo de Entrega e Vistoria',
                    'corpo_html' => $this->termoEntrega(),
                ]
            );

            ContratoModelo::firstOrCreate(
                ['empresa_id' => $empresa->id, 'tipo' => 'declaracao_quitacao'],
                [
                    'nome' => 'Declaração de Quitação',
                    'corpo_html' => $this->declaracaoQuitacao(),
                ]
            );

            ContratoModelo::firstOrCreate(
                ['empresa_id' => $empresa->id, 'tipo' => 'termo_reserva'],
                [
                    'nome' => 'Termo de Reserva de Veículo',
                    'corpo_html' => $this->termoReserva(),
                ]
            );

            ContratoModelo::firstOrCreate(
                ['empresa_id' => $empresa->id, 'tipo' => 'termo_distrato'],
                [
                    'nome' => 'Termo de Distrato',
                    'corpo_html' => $this->termoDistrato(),
                ]
            );

            ContratoModelo::firstOrCreate(
                ['empresa_id' => $empresa->id, 'tipo' => 'prestacao_servico'],
                [
                    'nome' => 'Contrato de Prestação de Serviço (Oficina/Preparação)',
                    'corpo_html' => $this->prestacaoServico(),
                ]
            );

            ContratoModelo::firstOrCreate(
                ['empresa_id' => $empresa->id, 'tipo' => 'termo_cautela'],
                [
                    'nome' => 'Termo de Cautela (Test Drive)',
                    'corpo_html' => $this->termoCautela(),
                ]
            );

            ContratoModelo::firstOrCreate(
                ['empresa_id' => $empresa->id, 'tipo' => 'contrato_comissao'],
                [
                    'nome' => 'Acordo de Comissão / Indicação',
                    'corpo_html' => $this->contratoComissao(),
                ]
            );

            ContratoModelo::firstOrCreate(
                ['empresa_id' => $empresa->id, 'tipo' => 'termo_consentimento_dados'],
                [
                    'nome' => 'Termo de Consentimento (LGPD)',
                    'corpo_html' => $this->termoConsentimentoDados(),
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

    protected function financiamento(): string
    {
        return <<<'HTML'
        <h2 style="text-align:center">TERMO DE INTERVENIÊNCIA - VENDA FINANCIADA</h2>
        <p><strong>VENDEDORA/INTERVENIENTE:</strong> {{empresa.nome}}, CNPJ {{empresa.cnpj}}.</p>
        <p><strong>COMPRADOR/FINANCIADO:</strong> {{cliente.nome}}, CPF {{cliente.cpf}}, residente em {{cliente.endereco}}.</p>
        <h3>DO OBJETO</h3>
        <p>Fica ajustada a venda do veículo {{veiculo.marca}} {{veiculo.modelo}} {{veiculo.versao}}, ano {{veiculo.ano}},
        placa {{veiculo.placa}}, chassi {{veiculo.chassi}}, no valor de {{valor.total}} ({{valor.extenso}}), a ser
        quitado por meio de financiamento contratado pelo comprador junto à instituição financeira de sua escolha.</p>
        <h3>DA INTERVENIÊNCIA</h3>
        <p>A vendedora intervém neste termo para declarar ciência das condições da operação e se compromete a entregar
        o veículo, a documentação de transferência e a nota fiscal/recibo assim que confirmado o repasse do valor
        financiado pela instituição financeira.</p>
        <p>{{data.hoje}}.</p>
        <br><br>
        <p>_________________________________<br>{{empresa.nome}}</p>
        <br>
        <p>_________________________________<br>{{cliente.nome}}</p>
        HTML;
    }

    protected function troca(): string
    {
        return <<<'HTML'
        <h2 style="text-align:center">TERMO DE TROCA / PERMUTA DE VEÍCULO</h2>
        <p><strong>REVENDA:</strong> {{empresa.nome}}, CNPJ {{empresa.cnpj}}.</p>
        <p><strong>CLIENTE:</strong> {{cliente.nome}}, CPF {{cliente.cpf}}, residente em {{cliente.endereco}}.</p>
        <h3>DO OBJETO</h3>
        <p>O cliente entrega à revenda, como parte de pagamento pela aquisição do veículo {{veiculo.marca}} {{veiculo.modelo}}
        {{veiculo.versao}}, placa {{veiculo.placa}}, chassi {{veiculo.chassi}}, seu veículo usado, recebendo em avaliação
        o desconto de {{valor.desconto}} sobre o valor total de {{valor.total}} ({{valor.extenso}}), restando o saldo a
        pagar conforme condição {{venda.forma_pagamento}}.</p>
        <h3>DAS DECLARAÇÕES DO CLIENTE</h3>
        <p>O cliente declara, sob as penas da lei, que o veículo entregue em troca está livre de ônus, gravames,
        multas ou débitos não informados, e que é o legítimo proprietário ou possui autorização para dele dispor.</p>
        <p>{{data.hoje}}.</p>
        <br><br>
        <p>_________________________________<br>{{empresa.nome}}</p>
        <br>
        <p>_________________________________<br>{{cliente.nome}}</p>
        HTML;
    }

    protected function garantiaEstendida(): string
    {
        return <<<'HTML'
        <h2 style="text-align:center">TERMO DE GARANTIA ESTENDIDA</h2>
        <p><strong>GARANTIDORA:</strong> {{empresa.nome}}, CNPJ {{empresa.cnpj}}.</p>
        <p><strong>CLIENTE:</strong> {{cliente.nome}}, CPF {{cliente.cpf}}.</p>
        <h3>DO VEÍCULO</h3>
        <p>{{veiculo.marca}} {{veiculo.modelo}} {{veiculo.versao}}, ano {{veiculo.ano}}, placa {{veiculo.placa}}, chassi {{veiculo.chassi}}.</p>
        <h3>DA COBERTURA</h3>
        <p>A garantidora estende a garantia contratual do veículo acima por {{venda.garantia_dias}} dias adicionais,
        contados a partir de {{venda.data}}, cobrindo os itens de motor, câmbio e sistema elétrico principal, conforme
        condições gerais entregues ao cliente em separado, excluídos itens de desgaste natural (pneus, freios, embreagem
        e correias) e danos decorrentes de mau uso ou acidente.</p>
        <p>{{data.hoje}}.</p>
        <br><br>
        <p>_________________________________<br>{{empresa.nome}}</p>
        <br>
        <p>_________________________________<br>{{cliente.nome}}</p>
        HTML;
    }

    protected function reciboSinal(): string
    {
        return <<<'HTML'
        <h2 style="text-align:center">RECIBO DE SINAL</h2>
        <p><strong>RECEBEDORA:</strong> {{empresa.nome}}, CNPJ {{empresa.cnpj}}.</p>
        <p><strong>PAGANTE:</strong> {{cliente.nome}}, CPF {{cliente.cpf}}.</p>
        <p>Recebemos de {{cliente.nome}} a quantia de {{valor.total}} ({{valor.extenso}}), a título de sinal e princípio
        de pagamento pela aquisição do veículo {{veiculo.marca}} {{veiculo.modelo}} {{veiculo.versao}}, placa {{veiculo.placa}},
        chassi {{veiculo.chassi}}, valor esse que será abatido do preço total ajustado na formalização da venda.</p>
        <p>Em caso de desistência por parte do comprador, o valor poderá ser retido a título de indenização, salvo
        acordo diverso entre as partes. Em caso de desistência por parte da vendedora, o valor será devolvido em dobro.</p>
        <p>{{data.hoje}}.</p>
        <br><br>
        <p>_________________________________<br>{{empresa.nome}}</p>
        HTML;
    }

    protected function termoEntrega(): string
    {
        return <<<'HTML'
        <h2 style="text-align:center">TERMO DE ENTREGA E VISTORIA</h2>
        <p><strong>VENDEDORA:</strong> {{empresa.nome}}, CNPJ {{empresa.cnpj}}.</p>
        <p><strong>COMPRADOR:</strong> {{cliente.nome}}, CPF {{cliente.cpf}}.</p>
        <h3>DO VEÍCULO ENTREGUE</h3>
        <p>{{veiculo.marca}} {{veiculo.modelo}} {{veiculo.versao}}, ano {{veiculo.ano}}, cor {{veiculo.cor}}, placa
        {{veiculo.placa}}, chassi {{veiculo.chassi}}, com {{veiculo.km}} km no odômetro na data de entrega.</p>
        <h3>DA VISTORIA</h3>
        <p>O comprador declara ter vistoriado o veículo neste ato, conferido a documentação, o manual, os pertences
        (chave reserva, quando houver) e o estado geral de funcionamento, recebendo-o em perfeitas condições de uso e
        sem ressalvas, exceto as anotadas abaixo:</p>
        <p>_________________________________________________________________</p>
        <p>{{data.hoje}}.</p>
        <br><br>
        <p>_________________________________<br>{{empresa.nome}}</p>
        <br>
        <p>_________________________________<br>{{cliente.nome}}</p>
        HTML;
    }

    protected function declaracaoQuitacao(): string
    {
        return <<<'HTML'
        <h2 style="text-align:center">DECLARAÇÃO DE QUITAÇÃO</h2>
        <p>{{empresa.nome}}, CNPJ {{empresa.cnpj}}, declara, para os devidos fins, que {{cliente.nome}}, CPF {{cliente.cpf}},
        efetuou o pagamento integral referente à aquisição do veículo {{veiculo.marca}} {{veiculo.modelo}} {{veiculo.versao}},
        placa {{veiculo.placa}}, chassi {{veiculo.chassi}}, no valor total de {{valor.total}} ({{valor.extenso}}), nada mais
        havendo a reclamar a qualquer título relacionado a essa transação.</p>
        <p>{{data.hoje}}.</p>
        <br><br>
        <p>_________________________________<br>{{empresa.nome}}</p>
        HTML;
    }

    protected function termoReserva(): string
    {
        return <<<'HTML'
        <h2 style="text-align:center">TERMO DE RESERVA DE VEÍCULO</h2>
        <p><strong>REVENDA:</strong> {{empresa.nome}}, CNPJ {{empresa.cnpj}}.</p>
        <p><strong>CLIENTE:</strong> {{cliente.nome}}, CPF {{cliente.cpf}}, telefone {{cliente.telefone}}.</p>
        <p>A revenda reserva, em caráter temporário, o veículo {{veiculo.marca}} {{veiculo.modelo}} {{veiculo.versao}},
        placa {{veiculo.placa}}, chassi {{veiculo.chassi}}, retirando-o de negociação com terceiros por até 48 (quarenta
        e oito) horas a contar de {{data.hoje}}, prazo dentro do qual o cliente deverá formalizar a compra, sob pena de
        o veículo voltar a ficar disponível para venda a qualquer interessado.</p>
        <p>{{data.hoje}}.</p>
        <br><br>
        <p>_________________________________<br>{{empresa.nome}}</p>
        <br>
        <p>_________________________________<br>{{cliente.nome}}</p>
        HTML;
    }

    protected function termoDistrato(): string
    {
        return <<<'HTML'
        <h2 style="text-align:center">TERMO DE DISTRATO</h2>
        <p><strong>VENDEDORA:</strong> {{empresa.nome}}, CNPJ {{empresa.cnpj}}.</p>
        <p><strong>COMPRADOR:</strong> {{cliente.nome}}, CPF {{cliente.cpf}}.</p>
        <p>As partes, de comum acordo, resolvem desfazer a compra e venda do veículo {{veiculo.marca}} {{veiculo.modelo}}
        {{veiculo.versao}}, placa {{veiculo.placa}}, chassi {{veiculo.chassi}}, celebrada em {{venda.data}} pelo valor de
        {{valor.total}} ({{valor.extenso}}), retornando as partes ao estado anterior: o veículo devolvido à vendedora e o
        valor pago restituído ao comprador, conforme condições ajustadas em separado, nada mais tendo a reclamar uma
        da outra a qualquer título decorrente dessa transação.</p>
        <p>{{data.hoje}}.</p>
        <br><br>
        <p>_________________________________<br>{{empresa.nome}}</p>
        <br>
        <p>_________________________________<br>{{cliente.nome}}</p>
        HTML;
    }

    protected function prestacaoServico(): string
    {
        return <<<'HTML'
        <h2 style="text-align:center">CONTRATO DE PRESTAÇÃO DE SERVIÇO</h2>
        <p><strong>CONTRATADA:</strong> {{empresa.nome}}, CNPJ {{empresa.cnpj}}.</p>
        <p><strong>CONTRATANTE:</strong> {{cliente.nome}}, CPF {{cliente.cpf}}.</p>
        <h3>DO OBJETO</h3>
        <p>A contratada prestará serviços de revisão/preparação no veículo {{veiculo.marca}} {{veiculo.modelo}}
        {{veiculo.versao}}, placa {{veiculo.placa}}, chassi {{veiculo.chassi}}, conforme orçamento previamente
        aprovado pelo contratante.</p>
        <h3>DO VALOR E PRAZO</h3>
        <p>Pelos serviços, o contratante pagará o valor de {{valor.total}} ({{valor.extenso}}), {{venda.forma_pagamento}},
        com prazo estimado de execução a combinar conforme a complexidade do serviço identificada na avaliação técnica.</p>
        <p>{{data.hoje}}.</p>
        <br><br>
        <p>_________________________________<br>{{empresa.nome}}</p>
        <br>
        <p>_________________________________<br>{{cliente.nome}}</p>
        HTML;
    }

    protected function termoCautela(): string
    {
        return <<<'HTML'
        <h2 style="text-align:center">TERMO DE CAUTELA - TEST DRIVE</h2>
        <p><strong>REVENDA:</strong> {{empresa.nome}}, CNPJ {{empresa.cnpj}}.</p>
        <p><strong>CONDUTOR:</strong> {{cliente.nome}}, CPF {{cliente.cpf}}.</p>
        <p>O condutor acima identificado, portador de Carteira Nacional de Habilitação válida, retira para test drive
        o veículo {{veiculo.marca}} {{veiculo.modelo}} {{veiculo.versao}}, placa {{veiculo.placa}}, chassi {{veiculo.chassi}},
        assumindo integral responsabilidade civil e criminal por eventuais danos, multas ou infrações ocorridas durante
        o período do teste, que terá duração aproximada de 30 (trinta) minutos, em trajeto previamente combinado com a
        revenda.</p>
        <p>{{data.hoje}}.</p>
        <br><br>
        <p>_________________________________<br>{{cliente.nome}}</p>
        HTML;
    }

    protected function contratoComissao(): string
    {
        return <<<'HTML'
        <h2 style="text-align:center">ACORDO DE COMISSÃO / INDICAÇÃO</h2>
        <p><strong>REVENDA:</strong> {{empresa.nome}}, CNPJ {{empresa.cnpj}}.</p>
        <p><strong>INDICADOR:</strong> {{cliente.nome}}, CPF {{cliente.cpf}}.</p>
        <p>A revenda pagará ao indicador uma comissão pela intermediação que resultou na venda do veículo
        {{veiculo.marca}} {{veiculo.modelo}} {{veiculo.versao}}, placa {{veiculo.placa}}, no valor de {{valor.total}}
        ({{valor.extenso}}), a ser quitada em até 30 (trinta) dias após a confirmação do recebimento integral do valor
        da venda pela revenda, mediante emissão de recibo pelo indicador.</p>
        <p>{{data.hoje}}.</p>
        <br><br>
        <p>_________________________________<br>{{empresa.nome}}</p>
        <br>
        <p>_________________________________<br>{{cliente.nome}}</p>
        HTML;
    }

    protected function termoConsentimentoDados(): string
    {
        return <<<'HTML'
        <h2 style="text-align:center">TERMO DE CONSENTIMENTO - TRATAMENTO DE DADOS PESSOAIS (LGPD)</h2>
        <p><strong>CONTROLADORA:</strong> {{empresa.nome}}, CNPJ {{empresa.cnpj}}.</p>
        <p><strong>TITULAR:</strong> {{cliente.nome}}, CPF {{cliente.cpf}}.</p>
        <p>O titular autoriza a controladora a coletar, armazenar e tratar seus dados pessoais (nome, CPF, RG, endereço,
        telefone, e-mail e dados do veículo negociado), com a finalidade exclusiva de formalizar a negociação, emitir
        documentos e contratos, e realizar contato relacionado à venda, garantia e pós-venda, nos termos da Lei
        13.709/2018 (LGPD). O titular pode solicitar, a qualquer momento, a exclusão ou correção de seus dados,
        ressalvadas as hipóteses de guarda obrigatória por prazo legal.</p>
        <p>{{data.hoje}}.</p>
        <br><br>
        <p>_________________________________<br>{{cliente.nome}}</p>
        HTML;
    }
}
