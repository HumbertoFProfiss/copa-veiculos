<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cnpj',
        'slug',
        'plano',
        'status',
        'trial_termina_em',
    ];

    protected function casts(): array
    {
        return [
            'trial_termina_em' => 'datetime',
        ];
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function modulosAtivos(): HasMany
    {
        return $this->hasMany(ModuloAtivo::class);
    }

    public function possuiModulo(string $slug): bool
    {
        return $this->modulosAtivos()->where('modulo_slug', $slug)->exists();
    }
}
