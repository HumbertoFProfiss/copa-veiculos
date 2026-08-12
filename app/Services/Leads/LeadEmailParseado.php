<?php

namespace App\Services\Leads;

class LeadEmailParseado
{
    public function __construct(
        public ?string $nome = null,
        public ?string $telefone = null,
        public ?string $email = null,
        public ?string $veiculoReferencia = null,
        public ?string $mensagem = null,
    ) {}

    public function valido(): bool
    {
        return filled($this->nome) && (filled($this->telefone) || filled($this->email));
    }
}
