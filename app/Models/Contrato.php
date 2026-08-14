<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contrato extends Model
{
    use BelongsToEmpresa;

    public const STATUS_LABELS = [
        'rascunho' => 'Rascunho',
        'enviado' => 'Enviado',
        'assinado' => 'Assinado',
        'cancelado' => 'Cancelado',
    ];

    protected $fillable = [
        'contrato_modelo_id', 'venda_id', 'veiculo_id', 'cliente_id', 'criado_por',
        'numero', 'status', 'corpo_html_gerado', 'pdf_path',
        'assinatura_provider', 'assinatura_status', 'assinatura_metadata',
    ];

    protected function casts(): array
    {
        return ['assinatura_metadata' => 'array'];
    }

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(ContratoModelo::class, 'contrato_modelo_id');
    }

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function statusVariant(): string
    {
        return match ($this->status) {
            'assinado' => 'success',
            'enviado' => 'warning',
            'cancelado' => 'error',
            default => 'neutral',
        };
    }

    public const ASSINATURA_STATUS_LABELS = [
        'nao_iniciada' => 'Não iniciada',
        'pendente' => 'Aguardando assinatura (simulação)',
        'assinado' => 'Assinado (simulação)',
        'recusado' => 'Recusado (simulação)',
    ];

    public function assinaturaStatusLabel(): string
    {
        return self::ASSINATURA_STATUS_LABELS[$this->assinatura_status] ?? $this->assinatura_status;
    }

    public function assinaturaStatusVariant(): string
    {
        return match ($this->assinatura_status) {
            'assinado' => 'success',
            'pendente' => 'warning',
            'recusado' => 'error',
            default => 'neutral',
        };
    }
}
