<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaFiscal extends Model
{
    use BelongsToEmpresa;

    protected $table = 'notas_fiscais';

    protected $fillable = [
        'venda_id', 'emitida_por', 'numero', 'serie', 'chave_acesso',
        'valor', 'status', 'emitida_em', 'cancelada_em',
    ];

    protected function casts(): array
    {
        return ['emitida_em' => 'datetime', 'cancelada_em' => 'datetime'];
    }

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }

    public function emissor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emitida_por');
    }

    /**
     * Chave de acesso fictícia (44 dígitos, formato só visual) - não é uma
     * chave real da SEFAZ, nunca consultável. Ver aviso de simulação na UI.
     */
    public static function gerarChaveAcessoSimulada(): string
    {
        $digitos = '';
        for ($i = 0; $i < 44; $i++) {
            $digitos .= random_int(0, 9);
        }

        return $digitos;
    }

    public function statusLabel(): string
    {
        return $this->status === 'cancelada' ? 'Cancelada' : 'Emitida (simulação)';
    }

    public function statusVariant(): string
    {
        return $this->status === 'cancelada' ? 'error' : 'success';
    }
}
