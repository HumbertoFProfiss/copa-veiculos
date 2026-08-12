<?php

namespace App\Services\Leads;

/**
 * Fallback pra qualquer portal sem parser dedicado ainda: extrai por regex
 * genérico de telefone (padrão brasileiro) e e-mail em qualquer lugar do
 * corpo, e usa a primeira linha não-vazia como nome (best-effort).
 */
class GenericEmailParser implements PortalEmailParser
{
    public function parse(string $assunto, string $corpo): LeadEmailParseado
    {
        $telefone = null;
        if (preg_match('/(?:\+?55\s?)?\(?\d{2}\)?[\s.-]?\d{4,5}[\s.-]?\d{4}/', $corpo, $m)) {
            $telefone = $m[0];
        }

        $email = null;
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $corpo, $m)) {
            $email = $m[0];
        }

        $nome = null;
        foreach (preg_split('/\r?\n/', trim($corpo)) as $linha) {
            $linha = trim($linha);
            if ($linha !== '' && ! str_contains($linha, '@') && strlen($linha) < 80) {
                $nome = $linha;

                break;
            }
        }

        return new LeadEmailParseado(
            nome: $nome,
            telefone: $telefone,
            email: $email,
            mensagem: $corpo,
        );
    }
}
