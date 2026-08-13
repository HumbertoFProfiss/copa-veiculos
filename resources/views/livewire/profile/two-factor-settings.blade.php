<div>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Autenticação em dois fatores (2FA)</h2>
        <p class="mt-1 text-sm text-gray-600">
            Adiciona uma camada extra de segurança: além da senha, o login pede um código do seu aplicativo autenticador (Google Authenticator, Authy etc).
        </p>
    </header>

    @if (session('sucesso-2fa'))
        <div class="mt-4 flex items-center gap-2.5 px-4 py-3 rounded-card bg-success/10 text-success text-sm border border-success/20">
            {{ session('sucesso-2fa') }}
        </div>
    @endif

    @if ($codigosRecuperacaoGerados)
        <div class="mt-4 p-4 rounded-card bg-warning/10 border border-warning/30">
            <p class="text-sm font-semibold text-text-primary mb-1">2FA ativado! Guarde esses códigos de recuperação.</p>
            <p class="text-xs text-text-secondary mb-3">Cada código só funciona uma vez e serve pra entrar se você perder acesso ao aplicativo autenticador. Não são mostrados de novo.</p>
            <div class="grid grid-cols-2 gap-2 font-mono text-sm bg-bg border border-border rounded-control p-3">
                @foreach ($codigosRecuperacaoGerados as $codigo)
                    <span>{{ $codigo }}</span>
                @endforeach
            </div>
            <button wire:click="fecharCodigosRecuperacao" type="button" class="mt-3 text-sm text-primary hover:underline">
                Já guardei, fechar
            </button>
        </div>
    @elseif (auth()->user()->possui2faAtivo())
        <div class="mt-4 flex items-center gap-2.5 px-4 py-3 rounded-card bg-success/10 text-success text-sm border border-success/20">
            2FA está ativo na sua conta.
        </div>

        <form wire:submit="desativar" class="mt-4 max-w-sm">
            <label class="block text-xs font-medium text-text-secondary mb-1">Confirme sua senha pra desativar</label>
            <input type="password" wire:model="senhaParaDesativar" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
            @error('senhaParaDesativar') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            <button type="submit" class="mt-2 px-4 py-2 rounded-control border border-error/30 text-error text-sm font-medium hover:bg-error/5">
                Desativar 2FA
            </button>
        </form>
    @elseif ($configurando)
        <div class="mt-4 max-w-sm">
            <p class="text-sm text-text-secondary mb-3">Escaneie o QR code com seu aplicativo autenticador e digite o código gerado pra confirmar.</p>
            <div class="bg-bg border border-border rounded-card p-4 inline-block">
                {!! $qrCodeSvg !!}
            </div>
            <form wire:submit="confirmar" class="mt-4">
                <label class="block text-xs font-medium text-text-secondary mb-1">Código de 6 dígitos</label>
                <input type="text" inputmode="numeric" wire:model="codigoConfirmacao" autocomplete="one-time-code"
                       class="w-full rounded-control border-border text-sm tracking-widest focus:border-primary focus:ring-primary">
                @error('codigoConfirmacao') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                <div class="flex items-center gap-2 mt-3">
                    <button type="submit" class="px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                        Confirmar e ativar
                    </button>
                    <button type="button" wire:click="cancelarConfiguracao" class="text-sm text-text-secondary hover:text-text-primary">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    @else
        <button wire:click="iniciarConfiguracao" type="button" class="mt-4 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
            Ativar 2FA
        </button>
    @endif
</div>
