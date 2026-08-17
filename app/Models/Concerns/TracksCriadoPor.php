<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Preenche criado_por sozinho na criacao (usuario autenticado no momento),
 * pra dar pra mostrar "quem fez" no feed de atividades recentes do
 * dashboard - mesmo padrao do BelongsToEmpresa, so que pra autoria.
 */
trait TracksCriadoPor
{
    public static function bootTracksCriadoPor(): void
    {
        static::creating(function ($model) {
            if (! $model->criado_por && auth()->check()) {
                $model->criado_por = auth()->id();
            }
        });
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }
}
