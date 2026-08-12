<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class ContratoModelo extends Model
{
    use BelongsToEmpresa;

    public const TIPO_LABELS = [
        'compra_venda' => 'Compra e Venda',
        'consignacao' => 'Consignação',
        'procuracao' => 'Procuração',
    ];

    protected $fillable = ['tipo', 'nome', 'corpo_html', 'versao', 'ativo'];

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }
}
