<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contato extends Model
{
    use BelongsToEmpresa;

    protected $fillable = ['nome', 'telefone_normalizado', 'email_normalizado'];

    public function origens(): HasMany
    {
        return $this->hasMany(LeadOrigem::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function portaisDeOrigem(): array
    {
        return $this->origens->pluck('portal')->unique()->values()->all();
    }
}
