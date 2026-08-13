<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'veiculo_id' => $this->veiculo_id,
            'cliente_id' => $this->cliente_id,
            'vendedor_id' => $this->vendedor_id,
            'forma_pagamento' => $this->forma_pagamento,
            'preco_venda' => $this->preco_venda,
            'status' => $this->status,
            'data_venda' => $this->data_venda,
            'criado_em' => $this->created_at?->toAtomString(),
        ];
    }
}
