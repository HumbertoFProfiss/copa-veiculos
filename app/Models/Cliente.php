<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use BelongsToEmpresa, HasFactory;

    protected $fillable = [
        'empresa_id', 'nome', 'cpf', 'rg', 'email', 'telefone', 'whatsapp',
        'endereco', 'cidade', 'uf', 'cep', 'profissao', 'renda_estimada', 'password',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'renda_estimada' => 'decimal:2',
            'password' => 'hashed',
        ];
    }

    public function vendas(): HasMany
    {
        return $this->hasMany(Venda::class);
    }
}
