<?php

namespace App\Jobs;

use App\Models\Empresa;
use App\Models\Publicacao;
use App\Models\Veiculo;
use App\Services\AdCanais\AdCanalAdapterResolver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DespublicarVeiculoEmCanal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [30, 120, 600];

    public function __construct(
        public int $empresaId,
        public int $publicacaoId,
    ) {}

    public function handle(AdCanalAdapterResolver $resolver): void
    {
        app()->instance('tenant', Empresa::findOrFail($this->empresaId));

        $publicacao = Publicacao::with('canal')->find($this->publicacaoId);

        if (! $publicacao || $publicacao->status !== 'publicado') {
            return;
        }

        $veiculo = Veiculo::findOrFail($publicacao->veiculo_id);
        $adapter = $resolver->resolve($publicacao->canal);

        try {
            $resultado = $adapter->despublicar($veiculo, $publicacao);
            $publicacao->update([
                'status' => $resultado->status,
                'ultima_sincronizacao_em' => now(),
            ]);
        } catch (\App\Services\AdCanais\Exceptions\AdCanalException $e) {
            $publicacao->update([
                'ultimo_erro' => $e->getMessage(),
                'ultima_sincronizacao_em' => now(),
            ]);
        }
    }
}
