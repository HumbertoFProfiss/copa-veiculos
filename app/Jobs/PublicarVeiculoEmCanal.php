<?php

namespace App\Jobs;

use App\Models\Canal;
use App\Models\Empresa;
use App\Models\Publicacao;
use App\Models\Veiculo;
use App\Services\AdCanais\AdCanalAdapterResolver;
use App\Services\AdCanais\Exceptions\AdCanalException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublicarVeiculoEmCanal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [30, 120, 600];

    public function __construct(
        public int $empresaId,
        public int $veiculoId,
        public int $canalId,
    ) {}

    public function handle(AdCanalAdapterResolver $resolver): void
    {
        app()->instance('tenant', Empresa::findOrFail($this->empresaId));

        $veiculo = Veiculo::with(['fotos', 'opcionais'])->findOrFail($this->veiculoId);
        $canal = Canal::findOrFail($this->canalId);

        $publicacao = Publicacao::firstOrNew(['veiculo_id' => $veiculo->id, 'canal_id' => $canal->id]);
        $adapter = $resolver->resolve($canal);

        try {
            if (! $adapter->estaConfigurado()) {
                throw new AdCanalException("Canal {$canal->nome} ainda não está configurado.");
            }

            $resultado = $adapter->publicar($veiculo, $publicacao->exists ? $publicacao : null);

            $publicacao->fill([
                ...$resultado->toArray(),
                'tentativas' => $publicacao->tentativas + 1,
                'ultima_sincronizacao_em' => now(),
            ])->save();
        } catch (AdCanalException $e) {
            $publicacao->fill([
                'status' => 'erro',
                'ultimo_erro' => $e->getMessage(),
                'tentativas' => $publicacao->tentativas + 1,
                'ultima_sincronizacao_em' => now(),
            ])->save();
        }
    }
}
