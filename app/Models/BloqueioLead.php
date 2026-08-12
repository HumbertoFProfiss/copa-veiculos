<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class BloqueioLead extends Model
{
    use BelongsToEmpresa;

    protected $table = 'bloqueios_lead';

    protected $fillable = ['tipo', 'valor', 'motivo'];
}
