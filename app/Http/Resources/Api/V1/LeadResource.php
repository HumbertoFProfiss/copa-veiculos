<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'origem' => $this->origem,
            'portal_origem' => $this->portal_origem,
            'estagio' => $this->estagio,
            'veiculo_id' => $this->veiculo_id,
            'vendedor_id' => $this->vendedor_id,
            'criado_em' => $this->created_at?->toAtomString(),
        ];
    }
}
