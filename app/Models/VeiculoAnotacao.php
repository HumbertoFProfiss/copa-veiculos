<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VeiculoAnotacao extends Model
{
    use BelongsToEmpresa;

    protected $table = 'veiculos_anotacoes';

    protected $fillable = ['veiculo_id', 'user_id', 'texto'];

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
