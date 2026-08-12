<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Papéis e permissões granulares por módulo (ver/criar/editar/excluir/aprovar),
 * conforme seção 3 do prompt. Permissões (Permission) são globais (compartilhadas
 * entre empresas via guard); PAPÉIS (Role) e a atribuição papel->permissão são
 * escopados por empresa (empresa_id, via a feature "teams" do Spatie configurada
 * em config/permission.php) - cada empresa pode, no futuro, customizar seus
 * próprios papéis sem afetar as demais.
 *
 * Lista de módulos reflete o que está em escopo pra Fase 1 (ver plano §4). Novos
 * módulos que forem sendo construídos (dashboard, ia...) podem adicionar suas
 * próprias permissões aqui incrementalmente.
 */
class RolePermissionSeeder extends Seeder
{
    protected array $modulos = [
        'veiculos',
        'clientes',
        'fornecedores',
        'vendas',
        'leads',
        'contratos',
        'financeiro',
        'anuncios',
        'importacoes',
        'usuarios',
        'configuracoes',
        'relatorios',
    ];

    protected array $acoes = ['ver', 'criar', 'editar', 'excluir', 'aprovar'];

    /**
     * Papel => [módulos liberados, ações liberadas].
     * '*' significa "todos os módulos"/"todas as ações" listados acima.
     */
    protected array $papeis = [
        'Proprietário' => ['modulos' => '*', 'acoes' => '*'],
        'Administrador' => ['modulos' => '*', 'acoes' => '*'],
        'Gerente' => [
            'modulos' => ['veiculos', 'clientes', 'fornecedores', 'vendas', 'leads', 'financeiro', 'relatorios', 'anuncios', 'contratos', 'importacoes'],
            'acoes' => ['ver', 'criar', 'editar', 'excluir'],
        ],
        'Vendedor' => [
            'modulos' => ['veiculos', 'clientes', 'leads', 'contratos'],
            'acoes' => ['ver', 'criar', 'editar'],
        ],
        'Fiscal/Contábil' => [
            'modulos' => ['financeiro', 'relatorios'],
            'acoes' => ['ver'],
        ],
        'Financeiro' => [
            'modulos' => ['financeiro'],
            'acoes' => ['ver', 'criar', 'editar', 'aprovar'],
        ],
        'Somente leitura' => [
            'modulos' => '*',
            'acoes' => ['ver'],
        ],
    ];

    public function run(): void
    {
        // Permissões são globais - criadas uma única vez, fora do loop de empresas.
        foreach ($this->modulos as $modulo) {
            foreach ($this->acoes as $acao) {
                Permission::findOrCreate("{$modulo}.{$acao}", 'web');
            }
        }

        $registrar = app(PermissionRegistrar::class);

        Empresa::all()->each(function (Empresa $empresa) use ($registrar) {
            $registrar->setPermissionsTeamId($empresa->id);

            foreach ($this->papeis as $nomePapel => $config) {
                $role = Role::findOrCreate($nomePapel, 'web');

                $modulosLiberados = $config['modulos'] === '*' ? $this->modulos : $config['modulos'];
                $acoesLiberadas = $config['acoes'] === '*' ? $this->acoes : $config['acoes'];

                $permissoes = [];
                foreach ($modulosLiberados as $modulo) {
                    foreach ($acoesLiberadas as $acao) {
                        $permissoes[] = "{$modulo}.{$acao}";
                    }
                }

                $role->syncPermissions($permissoes);
            }
        });
    }
}
