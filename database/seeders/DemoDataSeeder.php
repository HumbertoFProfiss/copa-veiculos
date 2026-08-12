<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

/**
 * Um usuário admin (papel Proprietário) por empresa de teste, pra login manual
 * no smoke test (ver plano §12). Senha única de demo - nunca usar em produção.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $registrar = app(PermissionRegistrar::class);

        Empresa::all()->each(function (Empresa $empresa) use ($registrar) {
            $user = User::query()->firstOrCreate(
                ['email' => "admin@{$empresa->slug}.test"],
                [
                    'empresa_id' => $empresa->id,
                    'name' => 'Admin '.$empresa->nome,
                    'password' => Hash::make('demo12345'),
                    'email_verified_at' => now(),
                    'ativo' => true,
                ]
            );

            $registrar->setPermissionsTeamId($empresa->id);
            $user->syncRoles(['Proprietário']);
        });
    }
}
