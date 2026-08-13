<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Endpoint de terceiro configurado por uma empresa pra receber eventos do
 * sistema (lead.recebido, venda.confirmada...) via POST assinado (ver
 * WebhookDispatcher/EntregarWebhook). Nao depende de credencial externa -
 * quem consome o webhook e o proprio cliente da empresa.
 */
class Webhook extends Model
{
    use BelongsToEmpresa;

    public const EVENTOS_DISPONIVEIS = [
        'lead.recebido' => 'Lead recebido',
        'venda.confirmada' => 'Venda confirmada',
    ];

    protected $fillable = ['empresa_id', 'url', 'eventos', 'secret', 'ativo'];

    protected function casts(): array
    {
        return [
            'eventos' => 'array',
            'ativo' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Webhook $webhook) {
            if (blank($webhook->secret)) {
                $webhook->secret = Str::random(40);
            }
        });
    }

    public function entregas(): HasMany
    {
        return $this->hasMany(WebhookEntrega::class)->latest();
    }

    public function escutaEvento(string $evento): bool
    {
        return $this->ativo && in_array($evento, $this->eventos ?? [], true);
    }
}
