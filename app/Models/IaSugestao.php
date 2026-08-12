<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class IaSugestao extends Model
{
    use BelongsToEmpresa;

    protected $table = 'ia_sugestoes';

    protected $fillable = [
        'tipo', 'sugerivel_type', 'sugerivel_id', 'conteudo_sugerido',
        'contexto_usado', 'status', 'usuario_decisao_id',
    ];

    protected function casts(): array
    {
        return ['contexto_usado' => 'array'];
    }

    public function sugerivel(): MorphTo
    {
        return $this->morphTo();
    }

    public function usuarioDecisao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_decisao_id');
    }
}
