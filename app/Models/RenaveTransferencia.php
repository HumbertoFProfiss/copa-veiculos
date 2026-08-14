<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RenaveTransferencia extends Model
{
    use BelongsToEmpresa;

    protected $table = 'renave_transferencias';

    protected $fillable = [
        'venda_id', 'gerada_por', 'protocolo', 'status', 'transferida_em', 'cancelada_em',
    ];

    protected function casts(): array
    {
        return ['transferida_em' => 'datetime', 'cancelada_em' => 'datetime'];
    }

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }

    public function geradaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gerada_por');
    }

    /**
     * Protocolo ficticio (formato so visual) - nao e um protocolo real do
     * Renave, nunca consultavel. Ver aviso de simulacao na UI.
     */
    public static function gerarProtocoloSimulado(): string
    {
        return 'RNV'.now()->format('y').str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function statusLabel(): string
    {
        return $this->status === 'cancelada' ? 'Cancelada' : 'Concluída (simulação)';
    }

    public function statusVariant(): string
    {
        return $this->status === 'cancelada' ? 'error' : 'success';
    }
}
