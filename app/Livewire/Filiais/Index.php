<?php

namespace App\Livewire\Filiais;

use App\Livewire\Concerns\WithDataTable;
use App\Models\Filial;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    public bool $mostrarForm = false;

    public ?int $editandoId = null;

    public string $nome = '';

    public string $endereco = '';

    public string $cidade = '';

    public string $uf = '';

    public string $telefone = '';

    public bool $ativa = true;

    protected function rules(): array
    {
        return [
            'nome' => 'required|string|max:150',
            'endereco' => 'nullable|string|max:200',
            'cidade' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'telefone' => 'nullable|string|max:20',
        ];
    }

    public function novo(): void
    {
        $this->authorize('filiais.criar');
        $this->reset(['editandoId', 'nome', 'endereco', 'cidade', 'uf', 'telefone']);
        $this->ativa = true;
        $this->mostrarForm = true;
    }

    public function editar(int $id): void
    {
        $this->authorize('filiais.editar');
        $filial = Filial::findOrFail($id);
        $this->editandoId = $filial->id;
        $this->fill($filial->only(['nome', 'endereco', 'cidade', 'uf', 'telefone', 'ativa']));
        $this->mostrarForm = true;
    }

    public function salvar(): void
    {
        $this->authorize($this->editandoId ? 'filiais.editar' : 'filiais.criar');

        $dados = $this->validate();
        $dados['ativa'] = $this->ativa;

        if ($this->editandoId) {
            Filial::findOrFail($this->editandoId)->update($dados);
        } else {
            Filial::create($dados);
        }

        $this->mostrarForm = false;
        session()->flash('sucesso', 'Filial salva com sucesso.');
    }

    public function excluir(int $id): void
    {
        $this->authorize('filiais.excluir');

        $filial = Filial::findOrFail($id);

        if ($filial->principal) {
            $this->addError('geral', 'Não é possível remover a filial principal.');

            return;
        }

        if ($filial->veiculos()->exists() || $filial->usuarios()->exists() || $filial->vendas()->exists()) {
            $this->addError('geral', 'Essa filial tem veículos, usuários ou vendas vinculados - mova-os antes de remover.');

            return;
        }

        $filial->delete();
        session()->flash('sucesso', 'Filial removida.');
    }

    protected function query(): Builder
    {
        return Filial::query()
            ->when($this->busca, fn (Builder $q) => $q->where('nome', 'like', "%{$this->busca}%"));
    }

    public function render()
    {
        return view('livewire.filiais.index', [
            'filiais' => $this->query()
                ->withCount(['veiculos', 'usuarios', 'vendas'])
                ->orderBy($this->ordenarPor, $this->ordenarDirecao)
                ->paginate($this->porPagina),
        ])->layout('layouts.admin', ['title' => 'Filiais']);
    }
}
