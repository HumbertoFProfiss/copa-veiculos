<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadTarefa extends Model
{
    use BelongsToEmpresa;

    protected $table = 'leads_tarefas';

    protected $fillable = ['lead_id', 'user_id', 'titulo', 'descricao', 'vencimento_em', 'concluida', 'concluida_em'];

    protected function casts(): array
    {
        return [
            'vencimento_em' => 'datetime',
            'concluida' => 'boolean',
            'concluida_em' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
