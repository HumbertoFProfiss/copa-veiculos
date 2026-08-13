<?php

namespace App\Livewire\Profile;

use App\Services\TwoFactor\TwoFactorAuthenticator;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class TwoFactorSettings extends Component
{
    public bool $configurando = false;

    public ?string $segredoTemporario = null;

    public ?string $qrCodeSvg = null;

    public string $codigoConfirmacao = '';

    public ?array $codigosRecuperacaoGerados = null;

    public string $senhaParaDesativar = '';

    public function iniciarConfiguracao(TwoFactorAuthenticator $autenticador): void
    {
        $this->segredoTemporario = $autenticador->gerarSegredo();
        $this->qrCodeSvg = $autenticador->qrCodeSvg(auth()->user(), $this->segredoTemporario);
        $this->configurando = true;
        $this->codigoConfirmacao = '';
    }

    public function cancelarConfiguracao(): void
    {
        $this->reset(['configurando', 'segredoTemporario', 'qrCodeSvg', 'codigoConfirmacao']);
    }

    public function confirmar(TwoFactorAuthenticator $autenticador): void
    {
        $this->validate(['codigoConfirmacao' => 'required|string']);

        if (! $autenticador->codigoValido($this->segredoTemporario, $this->codigoConfirmacao)) {
            $this->addError('codigoConfirmacao', 'Código inválido. Confira o app autenticador e tente de novo.');

            return;
        }

        $codigosRecuperacao = $autenticador->gerarCodigosRecuperacao();

        auth()->user()->forceFill([
            'two_factor_secret' => $this->segredoTemporario,
            'two_factor_recovery_codes' => $codigosRecuperacao,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->codigosRecuperacaoGerados = $codigosRecuperacao;
        $this->reset(['configurando', 'segredoTemporario', 'qrCodeSvg', 'codigoConfirmacao']);
    }

    public function fecharCodigosRecuperacao(): void
    {
        $this->codigosRecuperacaoGerados = null;
    }

    public function desativar(): void
    {
        $this->validate(['senhaParaDesativar' => 'required|string']);

        if (! Hash::check($this->senhaParaDesativar, auth()->user()->password)) {
            $this->addError('senhaParaDesativar', 'Senha incorreta.');

            return;
        }

        auth()->user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->senhaParaDesativar = '';
        session()->flash('sucesso-2fa', 'Autenticação em dois fatores desativada.');
    }

    public function render()
    {
        return view('livewire.profile.two-factor-settings');
    }
}
