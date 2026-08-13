<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filial extends Model
{
    use BelongsToEmpresa;

    protected $table = 'filiais';

    protected $fillable = [
        'empresa_id', 'nome', 'endereco', 'cidade', 'uf', 'telefone', 'principal', 'ativa',
    ];

    protected function casts(): array
    {
        return [
            'principal' => 'boolean',
            'ativa' => 'boolean',
        ];
    }

    public function veiculos(): HasMany
    {
        return $this->hasMany(Veiculo::class);
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function vendas(): HasMany
    {
        return $this->hasMany(Venda::class);
    }
}
