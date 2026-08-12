<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarroTroca extends Model
{
    use BelongsToEmpresa;

    protected $table = 'carros_troca';

    protected $fillable = ['venda_id', 'marca', 'modelo', 'ano_modelo', 'placa', 'km', 'valor_avaliado'];

    protected function casts(): array
    {
        return ['valor_avaliado' => 'decimal:2'];
    }

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }
}
