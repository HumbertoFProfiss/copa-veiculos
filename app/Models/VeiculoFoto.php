<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VeiculoFoto extends Model
{
    use BelongsToEmpresa;

    protected $table = 'veiculos_fotos';

    protected $fillable = ['veiculo_id', 'path', 'ordem', 'principal'];

    protected function casts(): array
    {
        return ['principal' => 'boolean'];
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function url(): string
    {
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->path);
    }
}
