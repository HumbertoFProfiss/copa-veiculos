<?php

namespace App\Services\Leads;

/**
 * IMPORTANTE: os rótulos de campo abaixo ("Nome:", "Telefone:", "E-mail:",
 * "Veículo:") são uma melhor estimativa de como notificações de lead de
 * portal costumam ser formatadas - nunca vimos um e-mail real do Webmotors.
 * Antes de usar em produção, conferir contra uma notificação real recebida
 * (a revenda já recebe esses e-mails hoje sem nenhuma integração nossa) e
 * ajustar os rótulos/regex aqui. Enquanto isso, GenericEmailParser cobre o
 * caso via regex genérico.
 */
class WebmotorsEmailParser implements PortalEmailParser
{
    public function parse(string $assunto, string $corpo): LeadEmailParseado
    {
        $extrair = function (string $rotulo) use ($corpo): ?string {
            if (preg_match('/'.preg_quote($rotulo, '/').'\s*:?\s*(.+)/i', $corpo, $m)) {
                return trim($m[1]);
            }

            return null;
        };

        $parseadoGenerico = (new GenericEmailParser)->parse($assunto, $corpo);

        return new LeadEmailParseado(
            nome: $extrair('Nome') ?? $parseadoGenerico->nome,
            telefone: $extrair('Telefone') ?? $parseadoGenerico->telefone,
            email: $extrair('E-mail') ?? $extrair('Email') ?? $parseadoGenerico->email,
            veiculoReferencia: $extrair('Veículo') ?? $extrair('Anúncio'),
            mensagem: $extrair('Mensagem') ?? $corpo,
        );
    }
}
