<?php

namespace App\Services\Leads;

/**
 * Identifica QUAL portal mandou o e-mail (pelo domínio do remetente) -
 * separado de QUAL parser sabe extrair os campos dele (ver PortalEmailParser).
 * Um portal sem parser dedicado ainda cai no GenericEmailParser pra extração,
 * mas o portal correto continua sendo registrado (não vira "site_proprio"
 * só porque não tem parser específico - isso jogaria fora justamente o dado
 * que a central de leads existe pra capturar: de qual portal cada lead veio).
 */
class PortalIdentificador
{
    protected const DOMINIOS = [
        'webmotors.com.br' => 'webmotors',
        'icarros.com.br' => 'icarros',
        'chavesnamao.com.br' => 'chavesnamao',
        'mobauto.com.br' => 'mobautos',
        'mobautos.com.br' => 'mobautos',
        'napista.com.br' => 'napista',
        'carrossp.com.br' => 'carrossp',
        'mercadolivre.com.br' => 'mercadolivre',
        'facebookmail.com' => 'facebook',
    ];

    public function identificar(string $remetente): string
    {
        $remetente = strtolower($remetente);

        foreach (self::DOMINIOS as $dominio => $portal) {
            if (str_contains($remetente, $dominio)) {
                return $portal;
            }
        }

        return 'site_proprio';
    }
}
