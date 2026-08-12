<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class CategoriaFinanceira extends Model
{
    use BelongsToEmpresa;

    protected $table = 'categorias_financeiras';

    protected $fillable = ['empresa_id', 'nome', 'tipo'];
}
