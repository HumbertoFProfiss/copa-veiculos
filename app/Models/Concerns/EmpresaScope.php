<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Filtra toda query por empresa_id = empresa atual (resolvida por subdomínio via
 * ResolveTenant, ver app/Http/Middleware/ResolveTenant.php). Fora de uma request
 * HTTP com tenant resolvido (comandos artisan, seeders) a query NÃO é filtrada -
 * scripts de sistema precisam poder operar sobre todas as empresas.
 *
 * Vazamento de dado entre empresas é o bug de segurança #1 num SaaS multi-tenant,
 * então esse scope é aplicado estruturalmente via o trait BelongsToEmpresa em todo
 * model de domínio, não deixado como responsabilidade de cada query individual.
 */
class EmpresaScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->bound('tenant') && app('tenant') !== null) {
            $builder->where($model->getTable().'.empresa_id', app('tenant')->id);
        }
    }
}
