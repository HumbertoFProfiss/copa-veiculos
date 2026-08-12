<?php

namespace App\Services\AdCanais;

class AdCanalResultado
{
    public function __construct(
        public string $status,
        public ?string $externalId = null,
        public ?string $urlAnuncio = null,
        public ?string $mensagemErro = null,
        public array $payloadEnviado = [],
    ) {}

    public static function publicado(?string $externalId = null, ?string $urlAnuncio = null, array $payload = []): self
    {
        return new self('publicado', $externalId, $urlAnuncio, null, $payload);
    }

    public static function despublicado(): self
    {
        return new self('despublicado');
    }

    public static function erro(string $mensagem, array $payload = []): self
    {
        return new self('erro', null, null, $mensagem, $payload);
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'external_id' => $this->externalId,
            'url_anuncio' => $this->urlAnuncio,
            'ultimo_erro' => $this->mensagemErro,
            'payload_enviado' => $this->payloadEnviado,
        ];
    }
}
