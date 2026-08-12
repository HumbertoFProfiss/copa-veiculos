<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadOrigem extends Model
{
    use BelongsToEmpresa;

    protected $table = 'lead_origens';

    protected $fillable = ['contato_id', 'portal', 'recebido_em'];

    protected function casts(): array
    {
        return ['recebido_em' => 'datetime'];
    }

    public function contato(): BelongsTo
    {
        return $this->belongsTo(Contato::class);
    }
}
