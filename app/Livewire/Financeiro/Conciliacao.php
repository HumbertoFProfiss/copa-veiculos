<?php

namespace App\Livewire\Financeiro;

use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\OfxImportacao;
use App\Models\OfxLancamento;
use App\Services\Financeiro\OfxParser;
use Livewire\Component;
use Livewire\WithFileUploads;

class Conciliacao extends Component
{
    use WithFileUploads;

    public $arquivo = null;

    public ?OfxImportacao $importacaoAtual = null;

    public function processarOfx(): void
    {
        $this->authorize('financeiro.criar');
        $this->validate(['arquivo' => 'required|file|max:5120']);

        $conteudo = file_get_contents($this->arquivo->getRealPath());
        $lancamentosParsed = (new OfxParser)->parse($conteudo);

        $importacao = OfxImportacao::create([
            'usuario_id' => auth()->id(),
            'arquivo_path' => null,
            'banco_nome' => 'Extrato importado',
            'total_lancamentos' => count($lancamentosParsed),
            'status' => 'concluido',
        ]);

        $conciliados = 0;

        foreach ($lancamentosParsed as $item) {
            $lancamento = OfxLancamento::create([
                'ofx_importacao_id' => $importacao->id,
                'data' => $item['data']->format('Y-m-d'),
                'descricao' => $item['descricao'],
                'valor' => $item['valor'],
                'tipo' => $item['tipo'],
            ]);

            $conciliadoCom = $this->tentarConciliar($lancamento);

            if ($conciliadoCom) {
                $conciliados++;
            }
        }

        $importacao->update(['total_conciliados' => $conciliados]);
        $this->importacaoAtual = $importacao;
        $this->arquivo = null;
    }

    /**
     * Concilia automaticamente se existir uma conta a pagar/receber com o
     * MESMO valor e vencimento dentro de 3 dias de diferença - critério
     * conservador (evita casar lançamentos errados). O que não bate fica
     * pra conciliação manual (fora do escopo desta primeira versão).
     */
    protected function tentarConciliar(OfxLancamento $lancamento): bool
    {
        if ($lancamento->tipo === 'debito') {
            $conta = ContaPagar::where('status', 'pendente')
                ->where('valor', $lancamento->valor)
                ->whereBetween('vencimento', [$lancamento->data->subDays(3), $lancamento->data->addDays(3)])
                ->first();

            if ($conta) {
                $conta->update(['status' => 'pago', 'pagamento' => $lancamento->data]);
                $lancamento->update(['conciliado' => true, 'contas_pagar_id' => $conta->id]);

                return true;
            }
        } else {
            $conta = ContaReceber::where('status', 'pendente')
                ->where('valor', $lancamento->valor)
                ->whereBetween('vencimento', [$lancamento->data->subDays(3), $lancamento->data->addDays(3)])
                ->first();

            if ($conta) {
                $conta->update(['status' => 'recebido', 'pagamento' => $lancamento->data]);
                $lancamento->update(['conciliado' => true, 'contas_receber_id' => $conta->id]);

                return true;
            }
        }

        return false;
    }

    public function render()
    {
        return view('livewire.financeiro.conciliacao', [
            'historico' => OfxImportacao::latest()->take(10)->get(),
        ])->layout('layouts.admin', ['title' => 'Conciliação Bancária']);
    }
}
