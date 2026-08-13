<?php

namespace App\Livewire\Veiculos;

use App\Models\Filial;
use App\Models\Fornecedor;
use App\Models\Veiculo;
use App\Models\VeiculoOpcional;
use App\Services\ConsultaPlaca\ConsultaPlacaException;
use App\Services\ConsultaPlaca\ConsultaPlacaService;
use Livewire\Component;

class Form extends Component
{
    public ?Veiculo $veiculo = null;

    public string $placaConsulta = '';

    public ?string $consultaPlacaErro = null;

    public ?string $consultaPlacaAlerta = null;

    public string $marca = '';

    public string $modelo = '';

    public ?string $versao = '';

    public ?string $descricao = '';

    public ?int $ano_fabricacao = null;

    public ?int $ano_modelo = null;

    public int $km = 0;

    public ?string $combustivel = '';

    public ?string $cambio = '';

    public ?string $cor = '';

    public ?int $portas = null;

    public ?string $motor = '';

    public ?string $placa = '';

    public ?string $numero_chassi = '';

    public ?string $renavam = '';

    public ?string $numero_estoque = '';

    public string $tipo_propriedade = 'proprio';

    public ?int $fornecedor_id = null;

    public ?int $filial_id = null;

    public ?string $consignado_nome = '';

    public ?string $consignado_telefone = '';

    public ?float $preco_compra = null;

    public ?float $preco_tabela_fipe = null;

    public ?float $preco_anuncio = null;

    public ?float $preco_venda = null;

    public ?float $preco_minimo = null;

    public ?string $situacao_documental = '';

    public bool $chave_reserva = false;

    public bool $manual = false;

    public ?string $estado_conservacao = '';

    public bool $gravame = false;

    public ?float $debitos = null;

    public bool $ipva_pago = false;

    public bool $licenciado = false;

    public ?string $local_patio = '';

    public string $status = 'em_preparacao';

    public bool $destaque = false;

    public string $novoOpcional = '';

    public function mount(?Veiculo $veiculo = null): void
    {
        if ($veiculo && $veiculo->exists) {
            $this->veiculo = $veiculo;
            $this->fill($veiculo->only([
                'marca', 'modelo', 'descricao', 'versao', 'ano_fabricacao', 'ano_modelo', 'km', 'combustivel',
                'cambio', 'cor', 'portas', 'motor', 'placa', 'numero_chassi', 'renavam', 'numero_estoque',
                'tipo_propriedade', 'fornecedor_id', 'filial_id', 'consignado_nome', 'consignado_telefone',
                'preco_compra', 'preco_tabela_fipe', 'preco_anuncio', 'preco_venda', 'preco_minimo',
                'situacao_documental', 'chave_reserva', 'manual', 'estado_conservacao', 'gravame',
                'debitos', 'ipva_pago', 'licenciado', 'local_patio', 'status', 'destaque',
            ]));
        } else {
            $this->filial_id = auth()->user()->filial_id;
        }
    }

    protected function rules(): array
    {
        return [
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'descricao' => 'nullable|string|max:5000',
            'versao' => 'nullable|string|max:100',
            'ano_fabricacao' => 'nullable|integer|min:1950|max:'.(date('Y') + 1),
            'ano_modelo' => 'nullable|integer|min:1950|max:'.(date('Y') + 1),
            'km' => 'required|integer|min:0',
            'combustivel' => 'nullable|string|max:50',
            'cambio' => 'nullable|string|max:50',
            'cor' => 'nullable|string|max:50',
            'portas' => 'nullable|integer|min:2|max:5',
            'motor' => 'nullable|string|max:50',
            'placa' => 'nullable|string|max:8',
            'numero_chassi' => 'nullable|string|max:30',
            'renavam' => 'nullable|string|max:20',
            'numero_estoque' => 'nullable|string|max:50',
            'tipo_propriedade' => 'required|in:proprio,consignado,terceiro',
            'fornecedor_id' => 'nullable|exists:fornecedores,id',
            'filial_id' => 'nullable|exists:filiais,id',
            'consignado_nome' => 'nullable|string|max:150',
            'consignado_telefone' => 'nullable|string|max:20',
            'preco_compra' => 'nullable|numeric|min:0',
            'preco_tabela_fipe' => 'nullable|numeric|min:0',
            'preco_anuncio' => 'nullable|numeric|min:0',
            'preco_venda' => 'nullable|numeric|min:0',
            'preco_minimo' => 'nullable|numeric|min:0',
            'situacao_documental' => 'nullable|string|max:150',
            'estado_conservacao' => 'nullable|string|max:150',
            'debitos' => 'nullable|numeric|min:0',
            'local_patio' => 'nullable|string|max:100',
            'status' => 'required|in:em_preparacao,disponivel,reservado,vendido,entregue,devolvido',
        ];
    }

    public function buscarPorPlaca(): void
    {
        $this->consultaPlacaErro = null;
        $this->consultaPlacaAlerta = null;

        try {
            $dados = app(ConsultaPlacaService::class)->consultar($this->placaConsulta);
        } catch (ConsultaPlacaException $e) {
            $this->consultaPlacaErro = $e->getMessage();

            return;
        }

        foreach (['marca', 'modelo', 'versao', 'ano_fabricacao', 'ano_modelo', 'cor', 'combustivel', 'cambio', 'numero_chassi', 'preco_tabela_fipe'] as $campo) {
            if (filled($dados[$campo] ?? null)) {
                $this->{$campo} = $dados[$campo];
            }
        }

        $this->placa = strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', $this->placaConsulta));

        if ($dados['restricao']) {
            $this->consultaPlacaAlerta = 'Atenção: consta restrição para esse veículo ("'.$dados['situacao'].'"). Confira antes de prosseguir.';
        }
    }

    public function adicionarOpcional(): void
    {
        if (! $this->veiculo) {
            return;
        }

        $this->validateOnly('novoOpcional', ['novoOpcional' => 'required|string|max:100']);

        $this->veiculo->opcionais()->create(['nome' => $this->novoOpcional]);
        $this->novoOpcional = '';
        $this->veiculo->refresh();
    }

    public function removerOpcional(int $id): void
    {
        VeiculoOpcional::where('id', $id)->delete();
        $this->veiculo?->refresh();
    }

    public function alternarOpcionalCatalogo(int $catalogoId): void
    {
        if (! $this->veiculo) {
            return;
        }

        $existente = $this->veiculo->opcionais()->where('opcional_catalogo_id', $catalogoId)->first();

        if ($existente) {
            $existente->delete();
        } else {
            $catalogo = \App\Models\OpcionalCatalogo::findOrFail($catalogoId);
            $this->veiculo->opcionais()->create([
                'opcional_catalogo_id' => $catalogo->id,
                'nome' => $catalogo->nome,
            ]);
        }

        $this->veiculo->refresh();
    }

    public function salvar()
    {
        $this->authorize($this->veiculo ? 'veiculos.editar' : 'veiculos.criar');

        $dados = $this->validate();

        if ($this->veiculo) {
            $this->veiculo->update($dados);
            $veiculo = $this->veiculo;
        } else {
            $veiculo = Veiculo::create($dados);
            $this->veiculo = $veiculo;
        }

        session()->flash('sucesso', 'Veículo salvo com sucesso.');

        return redirect()->route('admin.veiculos.editar', $veiculo);
    }

    public function render()
    {
        return view('livewire.veiculos.form', [
            'fornecedores' => Fornecedor::where('ativo', true)->orderBy('nome')->get(),
            'filiais' => Filial::where('ativa', true)->orderBy('nome')->get(),
            'opcionaisCatalogo' => \App\Models\OpcionalCatalogo::orderBy('ordem')->get(),
        ])->layout('layouts.admin', ['title' => $this->veiculo ? 'Editar veículo' : 'Novo veículo']);
    }
}
