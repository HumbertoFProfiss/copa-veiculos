<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use App\Models\Concerns\TracksCriadoPor;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model implements AuthenticatableContract
{
    use Authenticatable, BelongsToEmpresa, HasFactory, TracksCriadoPor;

    public const MEIO_DIVULGACAO_LABELS = [
        'indicacao' => 'Indicação',
        'instagram' => 'Instagram',
        'facebook' => 'Facebook',
        'google' => 'Google',
        'site' => 'Site',
        'passou_na_loja' => 'Passou na loja',
        'cliente_antigo' => 'Já foi cliente',
        'outros' => 'Outros',
    ];

    protected $fillable = [
        'empresa_id', 'criado_por', 'nome', 'cpf', 'rg', 'email', 'telefone', 'whatsapp',
        'endereco', 'cidade', 'uf', 'cep', 'profissao', 'renda_estimada', 'password',
        'data_nascimento', 'meio_divulgacao',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'renda_estimada' => 'decimal:2',
            'password' => 'hashed',
            'data_nascimento' => 'date',
        ];
    }

    public function meioDivulgacaoLabel(): ?string
    {
        return self::MEIO_DIVULGACAO_LABELS[$this->meio_divulgacao] ?? $this->meio_divulgacao;
    }

    public function vendas(): HasMany
    {
        return $this->hasMany(Venda::class);
    }

    public function favoritos(): HasMany
    {
        return $this->hasMany(ClienteFavorito::class);
    }

    public function garantiasChamados(): HasMany
    {
        return $this->hasMany(GarantiaChamado::class);
    }
}
