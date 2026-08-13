<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VeiculoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
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
            'preco_venda' => $this->preco_venda,
            'status' => $this->status,
            'destaque' => (bool) $this->destaque,
            'url_publica' => $this->status === 'disponivel' ? route('veiculo.show', $this->resource) : null,
            'criado_em' => $this->created_at?->toAtomString(),
            'atualizado_em' => $this->updated_at?->toAtomString(),
        ];
    }
}
