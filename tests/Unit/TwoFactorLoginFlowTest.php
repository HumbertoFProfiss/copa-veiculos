<?php

use App\Models\Empresa;
use App\Models\User;
use App\Services\TwoFactor\TwoFactorAuthenticator;
use PragmaRX\Google2FAQRCode\Google2FA;

// Sem RefreshDatabase (mesma razao do ConsultaPlacaServiceTest): usa o banco
// mysql real de dev (helper em tests/Pest.php). Cria e apaga um usuario
// proprio, nao mexe em dado existente.
uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

function hostTenantTeste(): string
{
    return 'empresa-a.'.config('tenancy.central_domain');
}

it('bloqueia o login direto e exige o codigo 2fa quando ativado', function () {
    $senha = 'senha-teste-123';

    $user = User::withoutGlobalScopes()->where('email', 'teste-2fa@copaveiculos.test')->first();
    $user?->delete();

    $empresa = \App\Models\Empresa::first();
    app()->instance('tenant', $empresa);

    $user = User::create([
        'empresa_id' => $empresa->id,
        'name' => 'Teste 2FA',
        'email' => 'teste-2fa@copaveiculos.test',
        'password' => $senha,
        'ativo' => true,
    ]);

    $autenticador = new TwoFactorAuthenticator;
    $segredo = $autenticador->gerarSegredo();
    $user->forceFill([
        'two_factor_secret' => $segredo,
        'two_factor_recovery_codes' => $autenticador->gerarCodigosRecuperacao(),
        'two_factor_confirmed_at' => now(),
    ])->save();

    $host = hostTenantTeste();

    // Login com senha certa nao deve ir direto pro dashboard - deve cair no desafio.
    $resposta = $this->withServerVariables(['HTTP_HOST' => $host])
        ->post('http://'.$host.'/login', [
            'email' => $user->email,
            'password' => $senha,
        ]);

    $resposta->assertRedirect(route('two-factor.challenge'));
    expect(auth()->check())->toBeFalse();
    expect(session('2fa_user_id'))->toBe($user->id);

    // Codigo errado falha.
    $respostaErrada = $this->withServerVariables(['HTTP_HOST' => $host])
        ->post('http://'.$host.'/two-factor-challenge', ['codigo' => '000000', 'recuperacao' => '0']);
    $respostaErrada->assertSessionHasErrors('codigo');
    expect(auth()->check())->toBeFalse();

    // Codigo certo completa o login.
    $codigoCerto = (new Google2FA)->getCurrentOtp($segredo);
    $respostaCerta = $this->withServerVariables(['HTTP_HOST' => $host])
        ->post('http://'.$host.'/two-factor-challenge', ['codigo' => $codigoCerto, 'recuperacao' => '0']);

    $respostaCerta->assertRedirect(route('dashboard', absolute: false));
    expect(auth()->id())->toBe($user->id);

    $user->delete();
});

it('permite login sem desafio quando o usuario nao tem 2fa ativo', function () {
    $senha = 'senha-teste-456';
    $empresa = \App\Models\Empresa::first();
    app()->instance('tenant', $empresa);

    User::withoutGlobalScopes()->where('email', 'sem-2fa@copaveiculos.test')->first()?->delete();

    $user = User::create([
        'empresa_id' => $empresa->id,
        'name' => 'Sem 2FA',
        'email' => 'sem-2fa@copaveiculos.test',
        'password' => $senha,
        'ativo' => true,
    ]);

    $host = hostTenantTeste();

    $resposta = $this->withServerVariables(['HTTP_HOST' => $host])
        ->post('http://'.$host.'/login', [
            'email' => $user->email,
            'password' => $senha,
        ]);

    $resposta->assertRedirect(route('dashboard', absolute: false));
    expect(auth()->id())->toBe($user->id);

    $user->delete();
});
