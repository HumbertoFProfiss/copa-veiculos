<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GarantiaChamado extends Model
{
    use BelongsToEmpresa;

    protected $table = 'garantias_chamados';

    public const STATUS_LABELS = [
        'aberto' => 'Aberto',
        'em_analise' => 'Em análise',
        'aprovado' => 'Aprovado',
        'recusado' => 'Recusado',
        'concluido' => 'Concluído',
    ];

    protected $fillable = [
        'empresa_id', 'venda_id', 'veiculo_id', 'cliente_id',
        'descricao_problema', 'status', 'custo_peca', 'custo_servico',
    ];

    protected function casts(): array
    {
        return [
            'custo_peca' => 'decimal:2',
            'custo_servico' => 'decimal:2',
        ];
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
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
}
