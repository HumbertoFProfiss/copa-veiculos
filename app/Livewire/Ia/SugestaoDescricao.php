<?php

namespace App\Livewire\Ia;

use App\Models\IaSugestao;
use App\Models\Veiculo;
use App\Services\Ia\AiProviderFactory;
use App\Services\Ia\DescricaoGerador;
use Livewire\Component;

class SugestaoDescricao extends Component
{
    public Veiculo $veiculo;

    public ?IaSugestao $sugestaoAtual = null;

    public function mount(): void
    {
        $this->sugestaoAtual = IaSugestao::where('sugerivel_type', Veiculo::class)
            ->where('sugerivel_id', $this->veiculo->id)
            ->where('tipo', 'descricao')
            ->where('status', 'pendente')
            ->latest()
            ->first();
    }

    public function solicitar(): void
    {
        $this->authorize('veiculos.editar');

        $gerador = new DescricaoGerador(AiProviderFactory::make());
        $this->sugestaoAtual = $gerador->gerar($this->veiculo, limiteCaracteres: 1000);
    }

    /**
     * "Usar" preenche o campo de descrição do formulário com o texto
     * sugerido - o vendedor ainda revisa e edita antes de salvar, a IA
     * nunca grava a descrição sozinha (só sugere).
     */
    public function usar(): void
    {
        $this->sugestaoAtual->update(['status' => 'aceita', 'usuario_decisao_id' => auth()->id()]);
        $this->dispatch('descricao-sugerida', descricao: $this->sugestaoAtual->conteudo_sugerido);
        $this->sugestaoAtual = null;
    }

    public function descartar(): void
    {
        $this->sugestaoAtual->update(['status' => 'descartada', 'usuario_decisao_id' => auth()->id()]);
        $this->sugestaoAtual = null;
    }

    public function render()
    {
        return view('livewire.ia.sugestao-descricao', [
            'iaDisponivel' => AiProviderFactory::make()->disponivel(),
        ]);
    }
}
