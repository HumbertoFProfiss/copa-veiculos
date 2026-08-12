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
}
