<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class MapeamentoImportacao extends Model
{
    use BelongsToEmpresa;

    protected $table = 'mapeamentos_importacao';

    protected $fillable = ['nome', 'origem', 'mapeamento'];

    protected function casts(): array
    {
        return ['mapeamento' => 'array'];
    }
}
