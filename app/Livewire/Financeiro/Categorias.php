<?php

namespace App\Livewire\Financeiro;

use App\Models\CategoriaFinanceira;
use Livewire\Component;

class Categorias extends Component
{
    public bool $mostrarForm = false;

    public ?int $editandoId = null;

    public string $nome = '';

    public string $tipo = 'despesa';

    public ?int $categoria_pai_id = null;

    protected function rules(): array
    {
        return [
            'nome' => 'required|string|max:100',
            'tipo' => 'required|in:receita,despesa',
            'categoria_pai_id' => 'nullable|exists:categorias_financeiras,id',
        ];
    }

    public function novo(?int $categoriaPaiId = null): void
    {
        $this->authorize('financeiro.criar');
        $this->reset(['editandoId', 'nome', 'categoria_pai_id']);
        $this->tipo = $categoriaPaiId
            ? CategoriaFinanceira::findOrFail($categoriaPaiId)->tipo
            : 'despesa';
        $this->categoria_pai_id = $categoriaPaiId;
        $this->mostrarForm = true;
    }

    public function editar(int $id): void
    {
        $this->authorize('financeiro.criar');
        $categoria = CategoriaFinanceira::findOrFail($id);
        $this->editandoId = $categoria->id;
        $this->fill($categoria->only(['nome', 'tipo', 'categoria_pai_id']));
        $this->mostrarForm = true;
    }

    public function salvar(): void
    {
        $this->authorize('financeiro.criar');

        $dados = $this->validate();

        if ($this->editandoId) {
            CategoriaFinanceira::findOrFail($this->editandoId)->update($dados);
        } else {
            CategoriaFinanceira::create($dados);
        }

        $this->mostrarForm = false;
        session()->flash('sucesso', 'Categoria salva com sucesso.');
    }

    public function excluir(int $id): void
    {
        $this->authorize('financeiro.criar');

        $categoria = CategoriaFinanceira::findOrFail($id);

        if ($categoria->subcategorias()->exists()) {
            session()->flash('erro', 'Essa categoria tem subcategorias — exclua ou mova as subcategorias antes.');

            return;
        }

        $categoria->delete();
        session()->flash('sucesso', 'Categoria removida.');
    }

    public function render()
    {
        $categorias = CategoriaFinanceira::whereNull('categoria_pai_id')
            ->with('subcategorias')
            ->orderBy('tipo')
            ->orderBy('nome')
            ->get();

        return view('livewire.financeiro.categorias', [
            'categorias' => $categorias,
            'todasCategorias' => CategoriaFinanceira::whereNull('categoria_pai_id')->orderBy('nome')->get(),
        ])->layout('layouts.admin', ['title' => 'Categorias financeiras']);
    }
}
