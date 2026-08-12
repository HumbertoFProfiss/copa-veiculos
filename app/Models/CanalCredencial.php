<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CanalCredencial extends Model
{
    use BelongsToEmpresa;

    protected $table = 'canal_credenciais';

    protected $fillable = ['canal_id', 'chave', 'valor'];

    protected function casts(): array
    {
        return ['valor' => 'encrypted'];
    }

    public function canal(): BelongsTo
    {
        return $this->belongsTo(Canal::class);
    }
}
