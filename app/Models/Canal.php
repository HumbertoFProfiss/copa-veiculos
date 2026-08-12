<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Canal extends Model
{
    // Catálogo global - não usa BelongsToEmpresa de propósito (ver migration).

    protected $table = 'canais';

    protected $fillable = ['slug', 'nome', 'tipo', 'ativo', 'config'];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'config' => 'array',
        ];
    }

    public function publicacoes(): HasMany
    {
        return $this->hasMany(Publicacao::class);
    }

    public function credenciais(): HasMany
    {
        return $this->hasMany(CanalCredencial::class);
    }
}
