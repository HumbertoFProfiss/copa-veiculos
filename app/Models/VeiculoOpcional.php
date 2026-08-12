<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VeiculoOpcional extends Model
{
    use BelongsToEmpresa;

    protected $table = 'veiculos_opcionais';

    protected $fillable = ['veiculo_id', 'nome'];

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }
}
