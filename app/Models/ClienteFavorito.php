<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteFavorito extends Model
{
    use BelongsToEmpresa;

    protected $table = 'cliente_favoritos';

    protected $fillable = ['empresa_id', 'cliente_id', 'veiculo_id'];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }
}
