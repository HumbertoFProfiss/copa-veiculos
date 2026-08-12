<?php

namespace App\Services\Leads;

/**
 * Um parser por portal - cada um sabe extrair nome/telefone/e-mail/veículo
 * da estrutura de e-mail de notificação daquele portal especificamente (ver
 * plano §8). GenericEmailParser cobre o que não tiver parser dedicado ainda,
 * com regex genérico de telefone/e-mail no corpo.
 */
interface PortalEmailParser
{
    public function parse(string $assunto, string $corpo): LeadEmailParseado;
}
