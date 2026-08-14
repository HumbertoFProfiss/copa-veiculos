<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>NF-e simulada {{ $nota->numero }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #0F172A; }
        .aviso { background: #FEF3C7; border: 2px solid #D97706; color: #92400E; padding: 12px; margin-bottom: 16px; font-weight: bold; text-align: center; font-size: 13px; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        td, th { border: 1px solid #E4E9F0; padding: 6px 8px; text-align: left; font-size: 11px; }
        th { background: #F7F9FC; }
        .chave { font-family: monospace; word-break: break-all; }
        .rodape { margin-top: 24px; font-size: 10px; color: #64748B; text-align: center; }
    </style>
</head>
<body>
    <div class="aviso">
        SIMULAÇÃO — DOCUMENTO SEM VALIDADE FISCAL OU JURÍDICA<br>
        Gerado apenas para demonstração do sistema. Não é uma NF-e autorizada pela SEFAZ.
    </div>

    <h1>Nota Fiscal Eletrônica (simulada)</h1>
    <p>Nº {{ $nota->numero }} — Série {{ $nota->serie }} — Emitida em {{ $nota->emitida_em->format('d/m/Y H:i') }}</p>

    <table>
        <tr><th colspan="2">Emitente</th></tr>
        <tr><td>Razão social</td><td>{{ $nota->venda->empresa?->nome ?? app('tenant')->nome }}</td></tr>
        <tr><td>CNPJ</td><td>{{ $nota->venda->empresa?->cnpj ?? app('tenant')->cnpj ?? '-' }}</td></tr>
    </table>

    <table>
        <tr><th colspan="2">Destinatário</th></tr>
        <tr><td>Nome</td><td>{{ $nota->venda->cliente?->nome }}</td></tr>
        <tr><td>CPF</td><td>{{ $nota->venda->cliente?->cpf ?? '-' }}</td></tr>
    </table>

    <table>
        <tr><th colspan="2">Item</th></tr>
        <tr>
            <td>Veículo</td>
            <td>{{ $nota->venda->veiculo?->marca }} {{ $nota->venda->veiculo?->modelo }} — placa {{ $nota->venda->veiculo?->placa ?? '-' }}</td>
        </tr>
        <tr><td>Valor total</td><td>R$ {{ number_format($nota->valor, 2, ',', '.') }}</td></tr>
    </table>

    <table>
        <tr><th>Chave de acesso (simulada — não consultável na SEFAZ)</th></tr>
        <tr><td class="chave">{{ $nota->chave_acesso }}</td></tr>
    </table>

    <p class="rodape">Documento gerado automaticamente pelo Copa Veículos em modo de simulação. Emitir uma NF-e válida exige certificado digital A1/A3 e credenciamento na SEFAZ do estado da empresa.</p>
</body>
</html>
