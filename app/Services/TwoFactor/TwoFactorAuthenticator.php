<?php

namespace App\Services\TwoFactor;

use App\Models\User;
use Illuminate\Support\Str;
use PragmaRX\Google2FAQRCode\Google2FA;

/**
 * TOTP (Google Authenticator / Authy / etc) - nao depende de nenhum
 * provedor externo, o codigo e gerado localmente a partir do segredo,
 * entao nao entra na categoria "trava por credencial de terceiro".
 *
 * two_factor_secret e two_factor_recovery_codes ficam com cast 'encrypted'
 * no model User - os metodos abaixo trabalham sempre com o valor em texto
 * puro (o proprio Eloquent cuida de criptografar/descriptografar no banco).
 */
class TwoFactorAuthenticator
{
    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA;
    }

    public function gerarSegredo(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function qrCodeSvg(User $user, string $segredo): string
    {
        return $this->google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $segredo,
        );
    }

    public function codigoValido(string $segredo, string $codigo): bool
    {
        return $this->google2fa->verifyKey($segredo, preg_replace('/\s+/', '', $codigo));
    }

    /**
     * @return array<int, string>
     */
    public function gerarCodigosRecuperacao(): array
    {
        return collect(range(1, 8))
            ->map(fn () => Str::random(10).'-'.Str::random(10))
            ->all();
    }

    public function consumirCodigoRecuperacao(User $user, string $codigo): bool
    {
        $codigos = $user->two_factor_recovery_codes ?? [];
        $codigo = trim($codigo);

        if (! in_array($codigo, $codigos, true)) {
            return false;
        }

        $user->forceFill([
            'two_factor_recovery_codes' => array_values(array_diff($codigos, [$codigo])),
        ])->save();

        return true;
    }
}
