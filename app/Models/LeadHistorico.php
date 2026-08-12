<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadHistorico extends Model
{
    use BelongsToEmpresa;

    protected $table = 'leads_historico';

    protected $fillable = ['lead_id', 'user_id', 'acao', 'detalhes'];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
