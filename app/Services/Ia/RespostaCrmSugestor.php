<?php

namespace App\Services\Ia;

use App\Models\IaSugestao;
use App\Models\Lead;

/**
 * Sugere resposta + resumo + "temperatura" do lead a partir do histórico
 * (ver plano §10) - o vendedor sempre aprova/edita antes de enviar, nunca
 * dispara mensagem sozinho.
 */
class RespostaCrmSugestor
{
    public function __construct(protected AiProvider $provider) {}

    public function sugerir(Lead $lead): IaSugestao
    {
        $historico = $lead->historico()->latest()->limit(10)->get(['acao', 'detalhes', 'created_at'])->reverse()->values();

        $contexto = [
            'nome' => $lead->nome,
            'estagio' => $lead->estagioLabel(),
            'veiculo' => $lead->veiculo ? "{$lead->veiculo->marca} {$lead->veiculo->modelo}" : null,
            'mensagem_original' => $lead->mensagem_original,
            'historico' => $historico->toArray(),
        ];

        $prompt = 'Com base neste histórico de atendimento, sugira: 1) uma resposta pronta pro vendedor mandar '
            .'agora, 2) um resumo de 1 frase da conversa, 3) a "temperatura" do lead (frio/morno/quente). '
            .'Dados: '.json_encode($contexto, JSON_UNESCAPED_UNICODE);

        $resposta = $this->provider->completar($prompt, $contexto);

        return IaSugestao::create([
            'tipo' => 'resposta_crm',
            'sugerivel_type' => Lead::class,
            'sugerivel_id' => $lead->id,
            'conteudo_sugerido' => $resposta,
            'contexto_usado' => $contexto,
            'status' => 'pendente',
        ]);
    }
}
