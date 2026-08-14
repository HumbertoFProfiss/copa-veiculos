<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Transferência Renave simulada {{ $transferencia->protocolo }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #0F172A; }
        .aviso { background: #FEF3C7; border: 2px solid #D97706; color: #92400E; padding: 12px; margin-bottom: 16px; font-weight: bold; text-align: center; font-size: 13px; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        td, th { border: 1px solid #E4E9F0; padding: 6px 8px; text-align: left; font-size: 11px; }
        th { background: #F7F9FC; }
        .protocolo { font-family: monospace; font-size: 14px; }
        .rodape { margin-top: 24px; font-size: 10px; color: #64748B; text-align: center; }
    </style>
</head>
<body>
    <div class="aviso">
        SIMULAÇÃO — DOCUMENTO SEM VALIDADE LEGAL<br>
        Gerado apenas para demonstração do sistema. Não é uma transferência real registrada no Renave/DENATRAN.
    </div>

    <h1>Comprovante de Transferência Renave (simulado)</h1>
    <p>Protocolo <span class="protocolo">{{ $transferencia->protocolo }}</span> — Registrado em {{ $transferencia->transferida_em->format('d/m/Y H:i') }}</p>

    <table>
        <tr><th colspan="2">Vendedor (revenda)</th></tr>
        <tr><td>Razão social</td><td>{{ $transferencia->venda->empresa?->nome ?? app('tenant')->nome }}</td></tr>
        <tr><td>CNPJ</td><td>{{ $transferencia->venda->empresa?->cnpj ?? app('tenant')->cnpj ?? '-' }}</td></tr>
    </table>

    <table>
        <tr><th colspan="2">Comprador</th></tr>
        <tr><td>Nome</td><td>{{ $transferencia->venda->cliente?->nome }}</td></tr>
        <tr><td>CPF</td><td>{{ $transferencia->venda->cliente?->cpf ?? '-' }}</td></tr>
    </table>

    <table>
        <tr><th colspan="2">Veículo transferido</th></tr>
        <tr><td>Veículo</td><td>{{ $transferencia->venda->veiculo?->marca }} {{ $transferencia->venda->veiculo?->modelo }}</td></tr>
        <tr><td>Placa</td><td>{{ $transferencia->venda->veiculo?->placa ?? '-' }}</td></tr>
        <tr><td>Chassi</td><td>{{ $transferencia->venda->veiculo?->numero_chassi ?? '-' }}</td></tr>
        <tr><td>Renavam</td><td>{{ $transferencia->venda->veiculo?->renavam ?? '-' }}</td></tr>
    </table>

    <p class="rodape">Documento gerado automaticamente pelo Copa Veículos em modo de simulação. Registrar uma transferência válida no Renave exige credenciamento no Registro Nacional de Veículos Automotores (DENATRAN).</p>
</body>
</html>
