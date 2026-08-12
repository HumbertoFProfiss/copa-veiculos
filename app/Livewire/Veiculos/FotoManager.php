<?php

namespace App\Livewire\Veiculos;

use App\Models\Veiculo;
use App\Models\VeiculoFoto;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class FotoManager extends Component
{
    use WithFileUploads;

    public Veiculo $veiculo;

    #[Validate('array|max:30')]
    public array $novasFotos = [];

    public function updatedNovasFotos(): void
    {
        $this->validate(['novasFotos.*' => 'image|max:8192']);

        $ordemAtual = (int) $this->veiculo->fotos()->max('ordem');
        $manager = new ImageManager(new Driver);

        foreach ($this->novasFotos as $arquivo) {
            $ordemAtual++;

            $nomeArquivo = 'veiculos/'.$this->veiculo->id.'/'.uniqid('foto_').'.webp';

            $imagemCodificada = $manager->read($arquivo->getRealPath())
                ->scaleDown(width: 1600)
                ->encode(new WebpEncoder(quality: 82));

            Storage::disk('public')->put($nomeArquivo, (string) $imagemCodificada);

            $this->veiculo->fotos()->create([
                'path' => $nomeArquivo,
                'ordem' => $ordemAtual,
                'principal' => $this->veiculo->fotos()->count() === 0,
            ]);
        }

        $this->novasFotos = [];
        $this->veiculo->refresh();
    }

    public function definirPrincipal(int $fotoId): void
    {
        $this->veiculo->fotos()->update(['principal' => false]);
        VeiculoFoto::where('id', $fotoId)->update(['principal' => true]);
        $this->veiculo->refresh();
    }

    public function remover(int $fotoId): void
    {
        $foto = VeiculoFoto::find($fotoId);

        if (! $foto) {
            return;
        }

        Storage::disk('public')->delete($foto->path);
        $eraPrincipal = $foto->principal;
        $foto->delete();

        if ($eraPrincipal) {
            $this->veiculo->fotos()->orderBy('ordem')->first()?->update(['principal' => true]);
        }

        $this->veiculo->refresh();
    }

    /**
     * Recebe a nova ordem completa (array de IDs de foto) do drag-and-drop no
     * front-end (ver resources/js/app.js) e persiste.
     */
    public function reordenar(array $idsEmOrdem): void
    {
        foreach ($idsEmOrdem as $posicao => $fotoId) {
            VeiculoFoto::where('id', $fotoId)
                ->where('veiculo_id', $this->veiculo->id)
                ->update(['ordem' => $posicao]);
        }

        $this->veiculo->refresh();
    }

    public function render()
    {
        return view('livewire.veiculos.foto-manager', [
            'fotos' => $this->veiculo->fotos()->orderBy('ordem')->get(),
        ]);
    }
}
