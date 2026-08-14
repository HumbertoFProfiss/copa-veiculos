<?php

namespace App\Livewire\Chamadas;

use App\Livewire\Concerns\WithDataTable;
use App\Models\ChamadaProposta;
use App\Models\Cliente;
use App\Models\Veiculo;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    #[Url(as: 'intencao')]
    public string $filtroIntencao = '';

    #[Url(as: 'resultado')]
    public string $filtroResultado = '';

    public bool $mostrarForm = false;

    public ?int $editandoId = null;

    public ?int $cliente_id = null;

    public ?int $veiculo_id = null;

    public string $veiculo_procurado = '';

    public string $tipo = 'whatsapp';

    public string $intencao = 'comprar';

    public string $resultado = 'sem_resposta';

    public string $observacoes = '';

    protected function rules(): array
    {
        return [
            'cliente_id' => 'nullable|exists:clientes,id',
            'veiculo_id' => 'nullable|exists:veiculos,id',
            'veiculo_procurado' => 'nullable|string|max:150',
            'tipo' => 'required|in:ligacao,whatsapp,presencial,email',
            'intencao' => 'required|in:comprar,vender,consignar,outros',
            'resultado' => 'required|in:sem_resposta,em_negociacao,fechado,perdido',
            'observacoes' => 'nullable|string|max:2000',
        ];
    }

    public function novo(): void
    {
        $this->authorize('leads.editar');
        $this->reset(['editandoId', 'cliente_id', 'veiculo_id', 'veiculo_procurado', 'observacoes']);
        $this->tipo = 'whatsapp';
        $this->intencao = 'comprar';
        $this->resultado = 'sem_resposta';
        $this->mostrarForm = true;
    }

    public function editar(int $id): void
    {
        $this->authorize('leads.editar');
        $chamada = ChamadaProposta::findOrFail($id);
        $this->editandoId = $chamada->id;
        $this->fill($chamada->only([
            'cliente_id', 'veiculo_id', 'veiculo_procurado', 'tipo', 'intencao', 'resultado', 'observacoes',
        ]));
        $this->mostrarForm = true;
    }

    public function salvar(): void
    {
        $this->authorize('leads.editar');

        $dados = $this->validate();

        if ($this->editandoId) {
            ChamadaProposta::findOrFail($this->editandoId)->update($dados);
        } else {
            $dados['user_id'] = auth()->id();
            ChamadaProposta::create($dados);
        }

        $this->mostrarForm = false;
        session()->flash('sucesso', 'Chamada registrada com sucesso.');
    }

    public function excluir(int $id): void
    {
        $this->authorize('leads.editar');
        ChamadaProposta::findOrFail($id)->delete();
        session()->flash('sucesso', 'Chamada removida.');
    }

    /**
     * Veículos do estoque atual que combinam (por marca/modelo) com o texto
     * livre do que o cliente está procurando - ajuda o atendente a ver na
     * hora se já existe algo parecido antes de anotar como "sem match".
     */
    public function getVeiculosCompativeisProperty()
    {
        if (blank($this->veiculo_procurado)) {
            return collect();
        }

        return Veiculo::where('status', 'disponivel')
            ->where(function (Builder $q) {
                foreach (preg_split('/\s+/', trim($this->veiculo_procurado)) as $palavra) {
                    if (mb_strlen($palavra) < 3) {
                        continue;
                    }
                    $q->orWhere('marca', 'like', "%{$palavra}%")
                        ->orWhere('modelo', 'like', "%{$palavra}%");
                }
            })
            ->limit(5)
            ->get();
    }

    protected function query(): Builder
    {
        return ChamadaProposta::query()
            ->with(['cliente', 'veiculo', 'atendente'])
            ->when($this->filtroIntencao, fn (Builder $q) => $q->where('intencao', $this->filtroIntencao))
            ->when($this->filtroResultado, fn (Builder $q) => $q->where('resultado', $this->filtroResultado));
    }

    public function render()
    {
        return view('livewire.chamadas.index', [
            'chamadas' => $this->query()->orderBy($this->ordenarPor, $this->ordenarDirecao)->paginate($this->porPagina),
            'clientes' => Cliente::orderBy('nome')->get(),
            'veiculos' => Veiculo::where('status', 'disponivel')->orderBy('marca')->get(),
        ])->layout('layouts.admin', ['title' => 'Chamadas e Propostas']);
    }
}
