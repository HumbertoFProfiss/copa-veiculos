<?php

namespace App\Services\Cep;

use Illuminate\Support\Facades\Http;

/**
 * Busca endereço a partir do CEP via ViaCEP (https://viacep.com.br) - API
 * pública gratuita, sem necessidade de token.
 */
class ConsultaCepService
{
    private const BASE_URL = 'https://viacep.com.br/ws';

    /**
     * @return array{endereco: string, cidade: string, uf: string}
     */
    public function consultar(string $cep): array
    {
        $cepNormalizado = preg_replace('/\D/', '', $cep);

        if (strlen((string) $cepNormalizado) !== 8) {
            throw new ConsultaCepException('CEP inválido. Use o formato 00000-000.');
        }

        $response = Http::timeout(10)->get(self::BASE_URL."/{$cepNormalizado}/json/");

        if ($response->failed()) {
            throw new ConsultaCepException('Não foi possível consultar o CEP agora. Tente novamente.');
        }

        $dados = $response->json();

        if (! is_array($dados) || ($dados['erro'] ?? false)) {
            throw new ConsultaCepException('CEP não encontrado.');
        }

        $logradouro = trim((string) ($dados['logradouro'] ?? ''));
        $bairro = trim((string) ($dados['bairro'] ?? ''));

        return [
            'endereco' => trim(implode(' - ', array_filter([$logradouro, $bairro]))),
            'cidade' => (string) ($dados['localidade'] ?? ''),
            'uf' => (string) ($dados['uf'] ?? ''),
        ];
    }
}
