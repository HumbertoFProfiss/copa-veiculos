<?php

namespace App\Livewire\Integracoes;

use App\Models\Canal;
use App\Models\CanalCredencial;
use App\Models\Publicacao;
use App\Models\Veiculo;
use App\Services\AdCanais\AdCanalAdapterResolver;
use Livewire\Component;

class Canais extends Component
{
    public ?int $canalCredencialAberto = null;

    public string $novaCredencialChave = '';

    public string $novaCredencialValor = '';

    public array $resultadoTeste = [];

    public string $filtroCanalLog = '';

    public function mount(): void
    {
        $this->authorize('integracoes.ver');
    }

    public function abrirCredenciais(int $canalId): void
    {
        $this->authorize('integracoes.ver');
        $this->canalCredencialAberto = $this->canalCredencialAberto === $canalId ? null : $canalId;
        $this->reset(['novaCredencialChave', 'novaCredencialValor']);
    }

    public function salvarCredencial(): void
    {
        $this->authorize('integracoes.editar');

        $this->validate([
            'novaCredencialChave' => 'required|string|max:100',
            'novaCredencialValor' => 'required|string|max:1000',
        ]);

        CanalCredencial::updateOrCreate(
            ['canal_id' => $this->canalCredencialAberto, 'chave' => $this->novaCredencialChave],
            ['valor' => $this->novaCredencialValor]
        );

        $this->reset(['novaCredencialChave', 'novaCredencialValor']);
        session()->flash('sucesso', 'Credencial salva.');
    }

    public function removerCredencial(int $id): void
    {
        $this->authorize('integracoes.editar');
        CanalCredencial::findOrFail($id)->delete();
        session()->flash('sucesso', 'Credencial removida.');
    }

    /**
     * "Testar conexão" honesto: não finge um handshake que os adapters atuais
     * não fazem (nenhum canal usa API/OAuth real ainda, ver AdCanalAdapter).
     * O teste real e útil hoje é confirmar que o adapter monta um payload
     * válido a partir de um veículo real - pega o mesmo tipo de erro que
     * apareceria numa publicação de verdade, sem publicar nada.
     */
    public function testarConexao(int $canalId): void
    {
        $this->authorize('integracoes.ver');

        $canal = Canal::findOrFail($canalId);
        $adapter = (new AdCanalAdapterResolver)->resolve($canal);

        if (! $adapter->estaConfigurado()) {
            $this->resultadoTeste[$canalId] = ['ok' => false, 'mensagem' => 'Canal ainda não configurado — falta credencial (ver abaixo).'];

            return;
        }

        $veiculoAmostra = Veiculo::where('status', 'disponivel')->first();

        if (! $veiculoAmostra) {
            $this->resultadoTeste[$canalId] = ['ok' => false, 'mensagem' => 'Nenhum veículo disponível no estoque pra testar o envio.'];

            return;
        }

        try {
            $payload = $adapter->montarPayload($veiculoAmostra);
            $this->resultadoTeste[$canalId] = [
                'ok' => true,
                'mensagem' => 'OK — payload gerado com '.count($payload).' campos, a partir de "'.$veiculoAmostra->marca.' '.$veiculoAmostra->modelo.'".',
            ];
        } catch (\Throwable $e) {
            $this->resultadoTeste[$canalId] = ['ok' => false, 'mensagem' => 'Erro ao montar o envio: '.$e->getMessage()];
        }
    }

    public function render()
    {
        $canais = Canal::where('ativo', true)->orderBy('nome')->get()->map(function (Canal $canal) {
            $adapter = (new AdCanalAdapterResolver)->resolve($canal);

            return [
                'canal' => $canal,
                'configurado' => $adapter->estaConfigurado(),
                'credenciais' => CanalCredencial::where('canal_id', $canal->id)->get(),
                'totalPublicados' => Publicacao::where('canal_id', $canal->id)->where('status', 'publicado')->count(),
                'totalErros' => Publicacao::where('canal_id', $canal->id)->where('status', 'erro')->count(),
            ];
        });

        $log = Publicacao::with(['veiculo', 'canal'])
            ->when($this->filtroCanalLog, fn ($q) => $q->where('canal_id', $this->filtroCanalLog))
            ->whereNotNull('ultima_sincronizacao_em')
            ->orderByDesc('ultima_sincronizacao_em')
            ->limit(30)
            ->get();

        return view('livewire.integracoes.canais', [
            'canais' => $canais,
            'log' => $log,
            'todosCanais' => Canal::where('ativo', true)->orderBy('nome')->get(),
        ])->layout('layouts.admin', ['title' => 'Canais de Anúncio']);
    }
}
