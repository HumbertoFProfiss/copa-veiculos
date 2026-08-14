<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    protected $fillable = ['slug', 'nome', 'taxa_juros_am_padrao', 'prazo_max_meses', 'ativo'];

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }
}
