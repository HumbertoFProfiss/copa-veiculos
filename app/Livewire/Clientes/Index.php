<?php

namespace App\Livewire\Clientes;

use App\Livewire\Concerns\WithDataTable;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    public bool $mostrarForm = false;

    public ?int $editandoId = null;

    public string $nome = '';

    public string $cpf = '';

    public string $email = '';

    public string $telefone = '';

    public string $whatsapp = '';

    protected function rules(): array
    {
        return [
            'nome' => 'required|string|max:150',
            'cpf' => 'nullable|string|max:14',
            'email' => 'nullable|email|max:150',
            'telefone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
        ];
    }

    public function novo(): void
    {
        $this->authorize('clientes.criar');
        $this->reset(['editandoId', 'nome', 'cpf', 'email', 'telefone', 'whatsapp']);
        $this->mostrarForm = true;
    }

    public function editar(int $id): void
    {
        $this->authorize('clientes.editar');
        $cliente = Cliente::findOrFail($id);
        $this->editandoId = $cliente->id;
        $this->fill($cliente->only(['nome', 'cpf', 'email', 'telefone', 'whatsapp']));
        $this->mostrarForm = true;
    }

    public function salvar(): void
    {
        $this->authorize($this->editandoId ? 'clientes.editar' : 'clientes.criar');

        $dados = $this->validate();

        if ($this->editandoId) {
            Cliente::findOrFail($this->editandoId)->update($dados);
        } else {
            Cliente::create($dados);
        }

        $this->mostrarForm = false;
        session()->flash('sucesso', 'Cliente salvo com sucesso.');
    }

    public function excluir(int $id): void
    {
        $this->authorize('clientes.excluir');
        Cliente::findOrFail($id)->delete();
        session()->flash('sucesso', 'Cliente removido.');
    }

    protected function query(): Builder
    {
        return Cliente::query()
            ->when($this->busca, function (Builder $q) {
                $termo = "%{$this->busca}%";
                $q->where(function (Builder $q) use ($termo) {
                    $q->where('nome', 'like', $termo)
                        ->orWhere('cpf', 'like', $termo)
                        ->orWhere('email', 'like', $termo)
                        ->orWhere('telefone', 'like', $termo);
                });
            });
    }

    public function render()
    {
        return view('livewire.clientes.index', [
            'clientes' => $this->query()->orderBy($this->ordenarPor, $this->ordenarDirecao)->paginate($this->porPagina),
        ])->layout('layouts.admin', ['title' => 'Clientes']);
    }
}
