<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactor\TwoFactorAuthenticator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Segunda etapa do login quando o usuario tem 2FA ativo. O login com
 * senha (AuthenticatedSessionController) so autentica de fato depois
 * que o codigo aqui e confirmado - ver session('2fa_user_id').
 */
class TwoFactorChallengeController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (! session('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request, TwoFactorAuthenticator $autenticador): RedirectResponse
    {
        $userId = session('2fa_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        $usandoRecuperacao = $request->boolean('recuperacao');

        $request->validate([
            $usandoRecuperacao ? 'codigo_recuperacao' : 'codigo' => ['required', 'string'],
        ]);

        $valido = $usandoRecuperacao
            ? $autenticador->consumirCodigoRecuperacao($user, $request->string('codigo_recuperacao')->toString())
            : $autenticador->codigoValido($user->two_factor_secret, $request->string('codigo')->toString());

        if (! $valido) {
            throw ValidationException::withMessages([
                'codigo' => 'Código inválido.',
            ]);
        }

        $remember = session('2fa_remember', false);
        session()->forget(['2fa_user_id', '2fa_remember']);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
