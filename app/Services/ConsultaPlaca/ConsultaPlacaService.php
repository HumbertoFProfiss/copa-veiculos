<?php

namespace App\Services\ConsultaPlaca;

use Illuminate\Support\Facades\Http;

/**
 * Busca dados de um veículo pela placa (API Placas / apiplacas.com.br,
 * documentada em https://apiplacas.com.br/doc.php - endpoint real em
 * wdapi2.com.br). Token único de plataforma (config('services.apiplacas.token')),
 * não por empresa - mesmo padrão do provider de IA.
 */
class ConsultaPlacaService
{
    private const BASE_URL = 'https://wdapi2.com.br';

    public function estaConfigurado(): bool
    {
        return filled(config('services.apiplacas.token'));
    }

    /**
     * @return array{
     *     marca: ?string, modelo: ?string, versao: ?string,
     *     ano_fabricacao: ?int, ano_modelo: ?int, cor: ?string,
     *     combustivel: ?string, cambio: ?string, numero_chassi: ?string,
     *     preco_tabela_fipe: ?float, fipe_referencia: ?string,
     *     situacao: ?string, restricao: bool,
     * }
     */
    public function consultar(string $placa): array
    {
        $token = config('services.apiplacas.token');

        if (blank($token)) {
            throw new ConsultaPlacaException('Token da API de consulta de placa não configurado.');
        }

        $placaNormalizada = strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', $placa));

        if ($placaNormalizada === '') {
            throw new ConsultaPlacaException('Informe uma placa.');
        }

        $response = Http::timeout(15)->get(self::BASE_URL."/consulta/{$placaNormalizada}/{$token}");

        if ($response->failed()) {
            throw new ConsultaPlacaException(match ($response->status()) {
                400 => 'URL de consulta inválida.',
                401 => 'Placa inválida. Use o formato AAA0A00 ou AAA0000.',
                402 => 'Token da API de consulta de placa inválido.',
                406 => 'Nenhum resultado encontrado para essa placa.',
                429 => 'Limite diário de consultas da API atingido.',
                default => $response->json('message') ?? 'Erro ao consultar a placa.',
            });
        }

        return $this->normalizar($response->json() ?? []);
    }

    private function normalizar(array $dados): array
    {
        $extra = $dados['extra'] ?? [];

        // Pode haver múltiplos valores de FIPE numa consulta; a doc recomenda
        // ficar com o de maior "score" (melhor correspondência de nome/marca).
        $melhorFipe = collect($dados['fipe']['dados'] ?? [])
            ->sortByDesc('score')
            ->first();

        $situacao = $dados['situacao'] ?? null;

        return [
            'marca' => $dados['marca'] ?? $dados['MARCA'] ?? null,
            'modelo' => $dados['modelo'] ?? $dados['MODELO'] ?? null,
            'versao' => $dados['VERSAO'] ?? $dados['SUBMODELO'] ?? null,
            'ano_fabricacao' => $this->paraInteiro($extra['ano_fabricacao'] ?? $dados['ano'] ?? null),
            'ano_modelo' => $this->paraInteiro($dados['anoModelo'] ?? $extra['ano_modelo'] ?? null),
            'cor' => $dados['cor'] ?? null,
            'combustivel' => filled($extra['combustivel'] ?? null) ? $extra['combustivel'] : null,
            'cambio' => filled($extra['caixa_cambio'] ?? null) ? $extra['caixa_cambio'] : null,
            'numero_chassi' => $dados['chassi'] ?? null,
            'preco_tabela_fipe' => $this->paraValorFipe($melhorFipe['texto_valor'] ?? null),
            'fipe_referencia' => $melhorFipe['mes_referencia'] ?? null,
            'situacao' => $situacao,
            'restricao' => filled($situacao) && ! str_contains(mb_strtolower($situacao), 'sem restri'),
        ];
    }

    private function paraInteiro(mixed $valor): ?int
    {
        return is_numeric($valor) ? (int) $valor : null;
    }

    private function paraValorFipe(?string $texto): ?float
    {
        if (blank($texto)) {
            return null;
        }

        // "R$ 28.799,00" -> 28799.00
        $numerico = str_replace(['R$', '.', ' '], '', $texto);
        $numerico = str_replace(',', '.', $numerico);

        return is_numeric($numerico) ? (float) $numerico : null;
    }
}
