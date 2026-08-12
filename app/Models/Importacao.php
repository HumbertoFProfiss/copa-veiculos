<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Importacao extends Model
{
    use BelongsToEmpresa;

    protected $table = 'importacoes';

    protected $fillable = [
        'user_id', 'origem', 'nome_arquivo_original', 'arquivo_path', 'status',
        'total_linhas', 'total_importados', 'total_duplicados', 'total_erros', 'mapeamento_usado',
    ];

    protected function casts(): array
    {
        return ['mapeamento_usado' => 'array'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function erros(): HasMany
    {
        return $this->hasMany(ImportacaoErro::class);
    }
}
