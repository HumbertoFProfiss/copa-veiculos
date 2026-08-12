<?php

namespace App\Support\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Contracts\PermissionsTeamResolver;

/**
 * Resolve pra empresa (tenant) atual por padrão, injetada no container pelo
 * middleware ResolveTenant - assim papéis/permissões do Spatie ficam
 * automaticamente escopados por empresa sem precisar chamar setPermissionsTeamId
 * manualmente em todo request. setPermissionsTeamId ainda funciona como override
 * explícito (útil em seeders/testes rodando fora de uma request HTTP).
 */
class EmpresaTeamResolver implements PermissionsTeamResolver
{
    protected int|string|null $teamId = null;

    protected bool $hasExplicitOverride = false;

    public function setPermissionsTeamId($id): void
    {
        if ($id instanceof Model) {
            $id = $id->getKey();
        }

        $this->teamId = $id;
        $this->hasExplicitOverride = true;
    }

    public function getPermissionsTeamId(): int|string|null
    {
        if ($this->hasExplicitOverride) {
            return $this->teamId;
        }

        return app()->bound('tenant') ? app('tenant')?->id : null;
    }
}
