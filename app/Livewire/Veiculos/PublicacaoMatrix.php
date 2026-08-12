<?php

namespace App\Livewire\Veiculos;

use App\Models\Canal;
use App\Models\Publicacao;
use App\Models\Veiculo;
use App\Jobs\DespublicarVeiculoEmCanal;
use App\Jobs\PublicarVeiculoEmCanal;
use Livewire\Component;

/**
 * UX obrigatória (seção 4.5 do prompt do chefe): os toggles de publicação
 * ficam dentro da própria tela de cadastro/edição do veículo, com
 * "Selecionar todas/Nenhuma" - esse é o "momento uau" da demonstração
 * comercial, não pode ficar escondido em submenu (ver plano §7).
 */
class PublicacaoMatrix extends Component
{
    public Veiculo $veiculo;

    public function togglar(int $canalId): void
    {
        $this->authorize('anuncios.criar');

        $publicacao = Publicacao::where('veiculo_id', $this->veiculo->id)->where('canal_id', $canalId)->first();

        // Só está "ligado" de verdade quando status=publicado - qualquer
        // outro estado (nunca criado, pendente, erro, despublicado) significa
        // que o clique deve tentar publicar, não despublicar.
        if ($publicacao?->status === 'publicado') {
            DespublicarVeiculoEmCanal::dispatchSync($this->veiculo->empresa_id, $publicacao->id);
        } else {
            PublicarVeiculoEmCanal::dispatchSync($this->veiculo->empresa_id, $this->veiculo->id, $canalId);
        }
    }

    public function selecionarTodas(): void
    {
        $this->authorize('anuncios.criar');

        foreach (Canal::where('ativo', true)->get() as $canal) {
            $publicacao = Publicacao::firstOrNew(['veiculo_id' => $this->veiculo->id, 'canal_id' => $canal->id]);

            if ($publicacao->status !== 'publicado') {
                PublicarVeiculoEmCanal::dispatchSync($this->veiculo->empresa_id, $this->veiculo->id, $canal->id);
            }
        }
    }

    public function nenhuma(): void
    {
        $this->authorize('anuncios.criar');

        Publicacao::where('veiculo_id', $this->veiculo->id)
            ->where('status', 'publicado')
            ->get()
            ->each(fn (Publicacao $p) => DespublicarVeiculoEmCanal::dispatchSync($this->veiculo->empresa_id, $p->id));
    }

    public function reprocessar(int $canalId): void
    {
        $this->authorize('anuncios.criar');
        PublicarVeiculoEmCanal::dispatchSync($this->veiculo->empresa_id, $this->veiculo->id, $canalId);
    }

    public function render()
    {
        $publicacoesPorCanal = Publicacao::where('veiculo_id', $this->veiculo->id)
            ->get()
            ->keyBy('canal_id');

        return view('livewire.veiculos.publicacao-matrix', [
            'canais' => Canal::where('ativo', true)->orderBy('nome')->get(),
            'publicacoesPorCanal' => $publicacoesPorCanal,
        ]);
    }
}
