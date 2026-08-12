<?php

namespace App\Jobs;

use App\Models\Empresa;
use App\Models\Lead;
use App\Services\Leads\GenericEmailParser;
use App\Services\Leads\LeadDeduplicator;
use App\Services\Leads\LeadFalsoDetector;
use App\Services\Leads\PortalEmailParser;
use App\Services\Leads\PortalIdentificador;
use App\Services\Leads\WebmotorsEmailParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Processa 1 e-mail de notificação de lead recebido de um portal (ver plano
 * §8). Identifica o portal pelo remetente, aplica o parser certo, deduplica
 * via LeadDeduplicator, roda o LeadFalsoDetector, e só então cria o Lead -
 * nunca cria 2x o mesmo contato nem passa reto por um número óbvio de spam.
 */
class ProcessarEmailLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [30, 120, 600];

    public function __construct(
        public int $empresaId,
        public string $remetente,
        public string $assunto,
        public string $corpo,
    ) {}

    /** Parser dedicado por portal - o que não estiver aqui cai no GenericEmailParser. */
    protected function parserPara(string $portal): PortalEmailParser
    {
        return match ($portal) {
            'webmotors' => new WebmotorsEmailParser,
            // Novos parsers dedicados (iCarros, Chaves na Mão...) entram aqui
            // conforme formatos reais de notificação forem confirmados.
            default => new GenericEmailParser,
        };
    }

    public function handle(): void
    {
        $empresa = Empresa::findOrFail($this->empresaId);
        app()->instance('tenant', $empresa);

        // O portal vem sempre do domínio do remetente (dado que sempre temos),
        // independente de existir ou não um parser dedicado pra extrair os
        // campos - ver PortalIdentificador.
        $portal = (new PortalIdentificador)->identificar($this->remetente);
        $parser = $this->parserPara($portal);

        $parseado = $parser->parse($this->assunto, $this->corpo);

        if (! $parseado->valido()) {
            return;
        }

        $deduplicator = new LeadDeduplicator;
        $telefoneNormalizado = $deduplicator->normalizarTelefone($parseado->telefone);
        $emailNormalizado = $deduplicator->normalizarEmail($parseado->email);

        $contato = $deduplicator->resolverContato(
            $parseado->nome,
            $parseado->telefone,
            $parseado->email,
            $portal,
        );

        $motivoBloqueio = (new LeadFalsoDetector)->detectar($telefoneNormalizado, $emailNormalizado);

        $lead = Lead::create([
            'contato_id' => $contato->id,
            'nome' => $parseado->nome,
            'telefone' => $parseado->telefone,
            'email' => $parseado->email,
            'origem' => $portal === 'site_proprio' ? 'site' : 'outro',
            'portal_origem' => $portal,
            'mensagem_original' => $parseado->mensagem,
            'estagio' => 'novo',
            'lead_falso' => $motivoBloqueio !== null,
            'motivo_bloqueio' => $motivoBloqueio,
        ]);

        event(new \App\Events\LeadRecebido($lead));
    }
}
