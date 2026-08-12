<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class PortalCaixaEntrada extends Model
{
    use BelongsToEmpresa;

    protected $table = 'portal_caixas_entrada';

    protected $fillable = ['portal', 'email_dedicado', 'ativo'];

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }
}
