<?php

namespace App\Livewire\Contratos;

use App\Models\Contrato;
use App\Models\ContratoModelo;
use App\Models\Venda;
use App\Services\Contratos\ContratoRenderer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Gerar extends Component
{
    public Venda $venda;

    public ?int $modeloId = null;

    public ?Contrato $contratoGerado = null;

    public function mount(Venda $venda): void
    {
        $this->venda = $venda;
        $this->modeloId = ContratoModelo::where('tipo', 'compra_venda')->where('ativo', true)->value('id');
    }

    public function getPreviewProperty(): ?string
    {
        if (! $this->modeloId) {
            return null;
        }

        $modelo = ContratoModelo::find($this->modeloId);

        if (! $modelo) {
            return null;
        }

        return (new ContratoRenderer)->renderizar($modelo->corpo_html, $this->venda, app('tenant'));
    }

    public function gerar(): void
    {
        $this->authorize('contratos.criar');
        $this->validate(['modeloId' => 'required|exists:contrato_modelos,id']);

        $modelo = ContratoModelo::findOrFail($this->modeloId);
        $corpoRenderizado = (new ContratoRenderer)->renderizar($modelo->corpo_html, $this->venda, app('tenant'));

        $numero = 'CTR-'.now()->format('Ymd').'-'.str_pad((string) (Contrato::withoutGlobalScopes()->count() + 1), 4, '0', STR_PAD_LEFT);

        $contrato = Contrato::create([
            'contrato_modelo_id' => $modelo->id,
            'venda_id' => $this->venda->id,
            'veiculo_id' => $this->venda->veiculo_id,
            'cliente_id' => $this->venda->cliente_id,
            'criado_por' => auth()->id(),
            'numero' => $numero,
            'status' => 'rascunho',
            'corpo_html_gerado' => $corpoRenderizado,
        ]);

        $pdf = Pdf::loadHTML($corpoRenderizado);
        $caminho = "contratos/{$contrato->id}.pdf";
        Storage::disk('local')->put($caminho, $pdf->output());

        $contrato->update(['pdf_path' => $caminho]);

        $this->contratoGerado = $contrato;
    }

    public function render()
    {
        return view('livewire.contratos.gerar', [
            'modelos' => ContratoModelo::where('ativo', true)->get(),
        ])->layout('layouts.admin', ['title' => 'Gerar contrato']);
    }
}
