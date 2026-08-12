<?php

namespace App\Livewire\Fornecedores;

use App\Livewire\Concerns\WithDataTable;
use App\Models\Fornecedor;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    public bool $mostrarForm = false;

    public ?int $editandoId = null;

    public string $tipo = 'revenda';

    public string $nome = '';

    public string $cpf_cnpj = '';

    public string $telefone = '';

    public string $email = '';

    public bool $ativo = true;

    protected function rules(): array
    {
        return [
            'tipo' => 'required|in:leiloeira,revenda,despachante,oficina,seguradora,outro',
            'nome' => 'required|string|max:150',
            'cpf_cnpj' => 'nullable|string|max:18',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
        ];
    }

    public function novo(): void
    {
        $this->authorize('fornecedores.criar');
        $this->reset(['editandoId', 'tipo', 'nome', 'cpf_cnpj', 'telefone', 'email']);
        $this->tipo = 'revenda';
        $this->ativo = true;
        $this->mostrarForm = true;
    }

    public function editar(int $id): void
    {
        $this->authorize('fornecedores.editar');
        $fornecedor = Fornecedor::findOrFail($id);
        $this->editandoId = $fornecedor->id;
        $this->fill($fornecedor->only(['tipo', 'nome', 'cpf_cnpj', 'telefone', 'email', 'ativo']));
        $this->mostrarForm = true;
    }

    public function salvar(): void
    {
        $this->authorize($this->editandoId ? 'fornecedores.editar' : 'fornecedores.criar');

        $dados = $this->validate();
        $dados['ativo'] = $this->ativo;

        if ($this->editandoId) {
            Fornecedor::findOrFail($this->editandoId)->update($dados);
        } else {
            Fornecedor::create($dados);
        }

        $this->mostrarForm = false;
        session()->flash('sucesso', 'Fornecedor salvo com sucesso.');
    }

    public function excluir(int $id): void
    {
        $this->authorize('fornecedores.excluir');
        Fornecedor::findOrFail($id)->delete();
        session()->flash('sucesso', 'Fornecedor removido.');
    }

    protected function query(): Builder
    {
        return Fornecedor::query()
            ->when($this->busca, fn (Builder $q) => $q->where('nome', 'like', "%{$this->busca}%"));
    }

    public function render()
    {
        return view('livewire.fornecedores.index', [
            'fornecedores' => $this->query()->orderBy($this->ordenarPor, $this->ordenarDirecao)->paginate($this->porPagina),
            'tipos' => Fornecedor::TIPO_LABELS,
        ])->layout('layouts.admin', ['title' => 'Fornecedores']);
    }
}
