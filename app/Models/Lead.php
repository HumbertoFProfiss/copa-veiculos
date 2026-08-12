<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use BelongsToEmpresa;

    public const ESTAGIO_LABELS = [
        'novo' => 'Novo',
        'em_atendimento' => 'Em atendimento',
        'proposta' => 'Proposta',
        'negociacao' => 'Negociação',
        'ganho' => 'Fechado ganho',
        'perdido' => 'Fechado perdido',
    ];

    protected $fillable = [
        'empresa_id', 'veiculo_id', 'vendedor_id', 'contato_id', 'nome', 'email', 'telefone', 'whatsapp',
        'origem', 'portal_origem', 'mensagem_original', 'estagio', 'lead_falso', 'motivo_bloqueio',
        'respondido_em', 'ip_origem', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'lead_falso' => 'boolean',
            'respondido_em' => 'datetime',
        ];
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function contato(): BelongsTo
    {
        return $this->belongsTo(Contato::class);
    }

    public function historico(): HasMany
    {
        return $this->hasMany(LeadHistorico::class)->latest();
    }

    public function tarefas(): HasMany
    {
        return $this->hasMany(LeadTarefa::class);
    }

    public function estagioLabel(): string
    {
        return self::ESTAGIO_LABELS[$this->estagio] ?? $this->estagio;
    }
}
