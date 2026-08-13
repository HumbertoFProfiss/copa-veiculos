<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookEntrega extends Model
{
    protected $fillable = [
        'webhook_id', 'evento', 'payload', 'status', 'tentativas',
        'resposta_http', 'resposta_corpo', 'enviado_em',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'enviado_em' => 'datetime',
        ];
    }

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }
}
