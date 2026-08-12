<?php

namespace App\Livewire\Concerns;

use Livewire\Attributes\Url;
use Livewire\WithPagination;

/**
 * Comportamento compartilhado por toda listagem administrativa: busca global,
 * ordenação por coluna, paginação server-side, filtros persistentes na URL e
 * seleção múltipla pra ações em lote. Ver plano §5 - cada módulo (Veiculos,
 * Clientes, Leads...) usa esse trait em vez de reimplementar tabela por tabela.
 */
trait WithDataTable
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $busca = '';

    #[Url(as: 'ordenar', history: true)]
    public string $ordenarPor = 'created_at';

    #[Url(as: 'direcao', history: true)]
    public string $ordenarDirecao = 'desc';

    public array $selecionados = [];

    public bool $selecionarTodos = false;

    public int $porPagina = 25;

    public function ordenar(string $coluna): void
    {
        if ($this->ordenarPor === $coluna) {
            $this->ordenarDirecao = $this->ordenarDirecao === 'asc' ? 'desc' : 'asc';
        } else {
            $this->ordenarPor = $coluna;
            $this->ordenarDirecao = 'asc';
        }

        $this->resetPage();
    }

    public function updatingBusca(): void
    {
        $this->resetPage();
    }

    public function updatedSelecionarTodos(bool $valor): void
    {
        if (! $valor) {
            $this->selecionados = [];

            return;
        }

        $this->selecionados = $this->linhasDaPaginaAtual()->pluck('id')->map(fn ($id) => (string) $id)->all();
    }

    public function limparSelecao(): void
    {
        $this->selecionados = [];
        $this->selecionarTodos = false;
    }

    /**
     * Cada componente que usa o trait implementa isso pra fornecer a query base
     * (sem paginar ainda) - o trait cuida de busca/ordenação/paginação em cima.
     */
    abstract protected function query(): \Illuminate\Database\Eloquent\Builder;

    protected function linhasDaPaginaAtual(): \Illuminate\Support\Collection
    {
        return $this->query()
            ->orderBy($this->ordenarPor, $this->ordenarDirecao)
            ->forPage($this->getPage(), $this->porPagina)
            ->get(['id']);
    }
}
