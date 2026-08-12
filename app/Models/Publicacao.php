<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Publicacao extends Model
{
    use BelongsToEmpresa;

    protected $table = 'publicacoes';

    protected $fillable = [
        'veiculo_id', 'canal_id', 'status', 'external_id', 'url_anuncio',
        'ultima_sincronizacao_em', 'ultimo_erro', 'payload_enviado', 'tentativas',
    ];

    protected function casts(): array
    {
        return [
            'ultima_sincronizacao_em' => 'datetime',
            'payload_enviado' => 'array',
        ];
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function canal(): BelongsTo
    {
        return $this->belongsTo(Canal::class);
    }
}
