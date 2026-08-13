<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaFinanceira extends Model
{
    use BelongsToEmpresa;

    protected $table = 'categorias_financeiras';

    protected $fillable = ['empresa_id', 'nome', 'tipo', 'categoria_pai_id'];

    public function pai(): BelongsTo
    {
        return $this->belongsTo(self::class, 'categoria_pai_id');
    }

    public function subcategorias(): HasMany
    {
        return $this->hasMany(self::class, 'categoria_pai_id');
    }

    public function nomeCompleto(): string
    {
        return $this->pai ? "{$this->pai->nome} / {$this->nome}" : $this->nome;
    }
}
