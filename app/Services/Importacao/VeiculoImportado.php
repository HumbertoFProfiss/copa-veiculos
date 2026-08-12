<?php

namespace App\Services\Importacao;

/**
 * DTO com os campos do NOSSO cadastro de veículo (ver App\Models\Veiculo),
 * já normalizados a partir de uma linha do arquivo de origem + o mapeamento
 * escolhido. Um ImportadorAdapter sempre devolve isso, independente do
 * formato de entrada (CSV do Boom, CSV genérico, XLSX...).
 */
class VeiculoImportado
{
    public function __construct(
        public ?string $marca = null,
        public ?string $modelo = null,
        public ?string $versao = null,
        public ?int $ano_fabricacao = null,
        public ?int $ano_modelo = null,
        public ?int $km = null,
        public ?string $combustivel = null,
        public ?string $cambio = null,
        public ?string $cor = null,
        public ?string $placa = null,
        public ?string $numero_chassi = null,
        public ?float $preco_venda = null,
        /** @var string[] URLs de fotos, se o export trouxer */
        public array $fotosUrls = [],
    ) {}

    public function paraArray(): array
    {
        return array_filter([
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'versao' => $this->versao,
            'ano_fabricacao' => $this->ano_fabricacao,
            'ano_modelo' => $this->ano_modelo,
            'km' => $this->km,
            'combustivel' => $this->combustivel,
            'cambio' => $this->cambio,
            'cor' => $this->cor,
            'placa' => $this->placa,
            'numero_chassi' => $this->numero_chassi,
            'preco_venda' => $this->preco_venda,
        ], fn ($valor) => $valor !== null);
    }

    public function valido(): bool
    {
        return filled($this->marca) && filled($this->modelo);
    }
}
