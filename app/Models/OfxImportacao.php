<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfxImportacao extends Model
{
    use BelongsToEmpresa;

    protected $table = 'ofx_importacoes';

    protected $fillable = [
        'usuario_id', 'arquivo_path', 'banco_nome', 'total_lancamentos', 'total_conciliados', 'status',
    ];

    public function lancamentos(): HasMany
    {
        return $this->hasMany(OfxLancamento::class);
    }
}
