<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportacaoErro extends Model
{
    use BelongsToEmpresa;

    protected $table = 'importacao_erros';

    protected $fillable = ['importacao_id', 'numero_linha', 'motivo', 'dados_originais'];

    protected function casts(): array
    {
        return ['dados_originais' => 'array'];
    }

    public function importacao(): BelongsTo
    {
        return $this->belongsTo(Importacao::class);
    }
}
