<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VeiculoVideo extends Model
{
    use BelongsToEmpresa;

    protected $table = 'veiculos_videos';

    protected $fillable = ['veiculo_id', 'tipo', 'url', 'path'];

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }

    /**
     * Converte um link comum do YouTube (watch?v=, youtu.be/, shorts/) pro
     * formato de embed usado no iframe da página pública.
     */
    public function urlEmbed(): ?string
    {
        if ($this->tipo !== 'youtube' || blank($this->url)) {
            return null;
        }

        $id = null;

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{6,})/', $this->url, $m)) {
            $id = $m[1];
        }

        return $id ? "https://www.youtube.com/embed/{$id}" : null;
    }
}
