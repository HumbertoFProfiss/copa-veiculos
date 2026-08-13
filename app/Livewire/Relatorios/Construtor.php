<?php

namespace App\Livewire\Relatorios;

use App\Services\Relatorios\FonteRelatorioRegistry;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Construtor de relatorios genericos: escolhe uma fonte (veiculos, vendas,
 * leads, clientes), quais campos mostrar, filtro de periodo, e opcionalmente
 * agrupa por um campo (com contagem + soma, quando a fonte tem um campo de
 * valor). Exporta o resultado exatamente como esta na tela (CSV).
 */
class Construtor extends Component
{
    use WithPagination;

    #[Url]
    public string $fonte = 'veiculos';

    #[Url]
    public array $camposSelecionados = [];

    #[Url]
    public string $agruparPor = '';

    #[Url]
    public string $dataInicio = '';

    #[Url]
    public string $dataFim = '';

    public function mount(): void
    {
        $this->authorize('relatorios.ver');

        if (empty($this->camposSelecionados)) {
            $this->camposSelecionados = array_keys(FonteRelatorioRegistry::fonte($this->fonte)['campos'] ?? []);
        }
    }

    public function updatedFonte(): void
    {
        $this->camposSelecionados = array_keys(FonteRelatorioRegistry::fonte($this->fonte)['campos'] ?? []);
        $this->agruparPor = '';
        $this->resetPage();
    }

    public function updatingAgruparPor(): void
    {
        $this->resetPage();
    }

    public function updatingDataInicio(): void
    {
        $this->resetPage();
    }

    public function updatingDataFim(): void
    {
        $this->resetPage();
    }

    protected function query(array $definicao): \Illuminate\Database\Eloquent\Builder
    {
        $model = $definicao['model'];

        return $model::query()
            ->when($this->dataInicio, fn ($q) => $q->whereDate($definicao['campo_data'], '>=', $this->dataInicio))
            ->when($this->dataFim, fn ($q) => $q->whereDate($definicao['campo_data'], '<=', $this->dataFim));
    }

    public function exportar()
    {
        $this->authorize('relatorios.ver');

        $definicao = FonteRelatorioRegistry::fonte($this->fonte);
        $linhas = $this->query($definicao)->get();

        return (new \App\Exports\RelatorioGenericoExport(
            $linhas,
            $this->camposSelecionados,
            $definicao['campos'],
            $definicao['labels_valor'] ?? [],
        ))->download("relatorio-{$this->fonte}-".now()->format('Y-m-d').'.xlsx');
    }

    public function render()
    {
        $definicao = FonteRelatorioRegistry::fonte($this->fonte);
        $query = $this->query($definicao);

        $agrupado = null;
        $linhas = null;

        if ($this->agruparPor && in_array($this->agruparPor, $definicao['campos_agrupaveis'] ?? [], true)) {
            $campoValor = $definicao['campo_valor'];
            $labelsCampo = $definicao['labels_valor'][$this->agruparPor] ?? [];

            $selecaoAgregada = $campoValor
                ? [$this->agruparPor, "COUNT(*) as quantidade", "SUM({$campoValor}) as soma"]
                : [$this->agruparPor, "COUNT(*) as quantidade"];

            $agrupado = $query->selectRaw(implode(', ', $selecaoAgregada))
                ->groupBy($this->agruparPor)
                ->orderByDesc('quantidade')
                ->get()
                ->map(function ($linha) use ($labelsCampo) {
                    $valorBruto = $linha->{$this->agruparPor};
                    $linha->rotulo = $labelsCampo[$valorBruto] ?? ($valorBruto === null ? '—' : (is_bool($valorBruto) ? ($valorBruto ? 'Sim' : 'Não') : $valorBruto));

                    return $linha;
                });
        } else {
            $linhas = $query->orderByDesc($definicao['campo_data'])->paginate(25);
        }

        return view('livewire.relatorios.construtor', [
            'fontes' => FonteRelatorioRegistry::fontes(),
            'definicao' => $definicao,
            'linhas' => $linhas,
            'agrupado' => $agrupado,
        ])->layout('layouts.admin', ['title' => 'Construtor de Relatórios']);
    }
}
