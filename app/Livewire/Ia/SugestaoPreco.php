<?php

namespace App\Livewire\Ia;

use App\Models\IaSugestao;
use App\Models\Veiculo;
use App\Services\Ia\AiProviderFactory;
use App\Services\Ia\PrecoSugestor;
use Livewire\Component;

class SugestaoPreco extends Component
{
    public Veiculo $veiculo;

    public ?IaSugestao $sugestaoAtual = null;

    public function mount(): void
    {
        $this->sugestaoAtual = IaSugestao::where('sugerivel_type', Veiculo::class)
            ->where('sugerivel_id', $this->veiculo->id)
            ->where('tipo', 'preco')
            ->where('status', 'pendente')
            ->latest()
            ->first();
    }

    public function solicitar(): void
    {
        $this->authorize('veiculos.editar');

        $sugestor = new PrecoSugestor(AiProviderFactory::make());
        $this->sugestaoAtual = $sugestor->sugerir($this->veiculo);
    }

    public function aceitar(): void
    {
        $this->sugestaoAtual->update(['status' => 'aceita', 'usuario_decisao_id' => auth()->id()]);
        $this->sugestaoAtual = null;
        $this->dispatch('sugestao-decidida');
    }

    public function descartar(): void
    {
        $this->sugestaoAtual->update(['status' => 'descartada', 'usuario_decisao_id' => auth()->id()]);
        $this->sugestaoAtual = null;
    }

    public function render()
    {
        return view('livewire.ia.sugestao-preco', [
            'iaDisponivel' => AiProviderFactory::make()->disponivel(),
        ]);
    }
}
