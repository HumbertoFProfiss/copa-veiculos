<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChamadaProposta extends Model
{
    use BelongsToEmpresa;

    protected $table = 'chamadas_proposta';

    public const TIPO_LABELS = [
        'ligacao' => 'Ligação',
        'whatsapp' => 'WhatsApp',
        'presencial' => 'Presencial',
        'email' => 'E-mail',
    ];

    public const INTENCAO_LABELS = [
        'comprar' => 'Comprar',
        'vender' => 'Vender',
        'consignar' => 'Consignar',
    ];

    public const RESULTADO_LABELS = [
        'sem_resposta' => 'Sem resposta',
        'em_negociacao' => 'Em negociação',
        'fechado' => 'Fechado',
        'perdido' => 'Perdido',
    ];

    protected $fillable = [
        'cliente_id', 'veiculo_id', 'veiculo_procurado', 'user_id',
        'tipo', 'intencao', 'resultado', 'observacoes',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function atendente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tipoLabel(): string
    {
        return self::TIPO_LABELS[$this->tipo] ?? $this->tipo;
    }

    public function intencaoLabel(): string
    {
        return self::INTENCAO_LABELS[$this->intencao] ?? $this->intencao;
    }

    public function resultadoLabel(): string
    {
        return self::RESULTADO_LABELS[$this->resultado] ?? $this->resultado;
    }

    public function resultadoVariant(): string
    {
        return match ($this->resultado) {
            'fechado' => 'success',
            'em_negociacao' => 'warning',
            'perdido' => 'error',
            default => 'neutral',
        };
    }
}
