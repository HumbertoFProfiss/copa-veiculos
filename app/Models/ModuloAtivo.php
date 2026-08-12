<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuloAtivo extends Model
{
    protected $table = 'modulos_ativos';

    protected $fillable = [
        'empresa_id',
        'modulo_slug',
        'ativado_em',
    ];

    protected function casts(): array
    {
        return [
            'ativado_em' => 'datetime',
        ];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
