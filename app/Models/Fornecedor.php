<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fornecedor extends Model
{
    use BelongsToEmpresa, HasFactory;

    protected $table = 'fornecedores';

    public const TIPO_LABELS = [
        'leiloeira' => 'Leiloeira',
        'revenda' => 'Revenda',
        'despachante' => 'Despachante',
        'oficina' => 'Oficina',
        'seguradora' => 'Seguradora',
        'outro' => 'Outro',
    ];

    protected $fillable = [
        'empresa_id', 'tipo', 'nome', 'cpf_cnpj', 'telefone', 'email',
        'endereco', 'cidade', 'uf', 'cep', 'observacoes', 'ativo',
    ];

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }

    public function veiculos(): HasMany
    {
        return $this->hasMany(Veiculo::class);
    }

    public function tipoLabel(): string
    {
        return self::TIPO_LABELS[$this->tipo] ?? $this->tipo;
    }
}
