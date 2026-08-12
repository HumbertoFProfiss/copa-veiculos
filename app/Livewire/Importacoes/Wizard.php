<?php

namespace App\Livewire\Importacoes;

use App\Models\Importacao;
use App\Models\ImportacaoErro;
use App\Models\Veiculo;
use App\Services\Importacao\BoomImportador;
use App\Services\Importacao\CsvGenericoImportador;
use App\Services\Importacao\ImportadorAdapter;
use App\Services\Importacao\MapeamentoResolver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Wizard extends Component
{
    use WithFileUploads;

    public string $etapa = 'upload';

    #[Validate('required|file|mimes:csv,txt|max:10240')]
    public $arquivo = null;

    public string $origem = 'csv_generico';

    public array $colunas = [];

    public array $linhas = [];

    public array $mapeamento = [];

    public ?Importacao $importacao = null;

    protected function adapter(): ImportadorAdapter
    {
        return match ($this->origem) {
            'boom' => new BoomImportador,
            default => new CsvGenericoImportador,
        };
    }

    public function processarUpload(): void
    {
        $this->validate();

        $conteudo = file($this->arquivo->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $linhasCsv = array_map(fn ($linha) => str_getcsv($linha, ';'), $conteudo);

        // Detecta separador (; ou ,) pela primeira linha
        if (count($linhasCsv[0] ?? []) <= 1) {
            $linhasCsv = array_map(fn ($linha) => str_getcsv($linha, ','), $conteudo);
        }

        $this->colunas = array_shift($linhasCsv);
        $this->linhas = array_map(fn ($linha) => array_combine($this->colunas, array_pad($linha, count($this->colunas), null)), $linhasCsv);

        $this->mapeamento = (new MapeamentoResolver)->sugerir($this->adapter(), $this->colunas);

        $this->etapa = 'mapeamento';
    }

    public function confirmarMapeamento(): void
    {
        $arquivoSalvo = $this->arquivo->store('importacoes', 'local');

        $this->importacao = Importacao::create([
            'user_id' => Auth::id(),
            'origem' => $this->origem,
            'nome_arquivo_original' => $this->arquivo->getClientOriginalName(),
            'arquivo_path' => $arquivoSalvo,
            'status' => 'importando',
            'total_linhas' => count($this->linhas),
            'mapeamento_usado' => $this->mapeamento,
        ]);

        $adapter = $this->adapter();
        $importados = 0;
        $duplicados = 0;
        $erros = 0;

        foreach ($this->linhas as $numero => $linha) {
            $veiculoImportado = $adapter->parseLinha($linha, $this->mapeamento);

            if (! $veiculoImportado->valido()) {
                ImportacaoErro::create([
                    'importacao_id' => $this->importacao->id,
                    'numero_linha' => $numero + 2,
                    'motivo' => 'Marca e modelo são obrigatórios',
                    'dados_originais' => $linha,
                ]);
                $erros++;

                continue;
            }

            $duplicado = false;

            if ($veiculoImportado->placa) {
                $duplicado = Veiculo::where('placa', $veiculoImportado->placa)->exists();
            }
            if (! $duplicado && $veiculoImportado->numero_chassi) {
                $duplicado = Veiculo::where('numero_chassi', $veiculoImportado->numero_chassi)->exists();
            }

            if ($duplicado) {
                $duplicados++;

                continue;
            }

            Veiculo::create([
                ...$veiculoImportado->paraArray(),
                'status' => 'em_preparacao',
            ]);

            $importados++;
        }

        $this->importacao->update([
            'status' => 'concluido',
            'total_importados' => $importados,
            'total_duplicados' => $duplicados,
            'total_erros' => $erros,
        ]);

        // Salva o mapeamento pra próxima importação da mesma origem já vir
        // pré-preenchido (ver MapeamentoResolver::sugerir) - evita repetir o
        // ajuste manual quando o mesmo cliente/formato for importado de novo.
        (new MapeamentoResolver)->salvar(
            $this->origem,
            'Mapeamento '.now()->format('d/m/Y H:i'),
            $this->mapeamento,
        );

        $this->etapa = 'concluido';
    }

    public function atualizarMapeamento(string $coluna, string $campoDestino): void
    {
        if ($campoDestino === '') {
            unset($this->mapeamento[$coluna]);
        } else {
            $this->mapeamento[$coluna] = $campoDestino;
        }
    }

    public function reiniciar(): void
    {
        $this->reset(['etapa', 'arquivo', 'colunas', 'linhas', 'mapeamento', 'importacao']);
    }

    public function render()
    {
        return view('livewire.importacoes.wizard', [
            'camposDestino' => $this->adapter()->camposDestino(),
            'historico' => Importacao::latest()->take(10)->get(),
        ])->layout('layouts.admin', ['title' => 'Importar estoque']);
    }
}
