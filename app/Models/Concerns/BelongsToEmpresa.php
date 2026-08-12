<?php

namespace App\Models\Concerns;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Aplicar em todo model de domínio multi-tenant (Veiculo, Cliente, Venda, Lead...).
 * Registra o EmpresaScope global (isolamento automático de leitura) e preenche
 * empresa_id sozinho na criação a partir do tenant atual - enforcement estrutural,
 * não por convenção, seguindo o mesmo padrão do status-badge no design system.
 */
trait BelongsToEmpresa
{
    public static function bootBelongsToEmpresa(): void
    {
        static::addGlobalScope(new EmpresaScope);

        static::creating(function ($model) {
            if (! $model->empresa_id && app()->bound('tenant') && app('tenant') !== null) {
                $model->empresa_id = app('tenant')->id;
            }
        });
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
