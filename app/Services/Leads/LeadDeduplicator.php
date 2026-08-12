<?php

namespace App\Services\Leads;

use App\Models\Contato;
use App\Models\LeadOrigem;

/**
 * Normaliza telefone/e-mail e resolve pra um Contato único dentro da mesma
 * empresa - mesmo telefone chegando via Webmotors e depois via iCarros vira
 * 1 contato com 2 linhas em lead_origens, não 2 leads duplicados (ver plano §8).
 */
class LeadDeduplicator
{
    public function normalizarTelefone(?string $telefone): ?string
    {
        if (blank($telefone)) {
            return null;
        }

        $digitos = preg_replace('/\D/', '', $telefone);

        // Remove DDI 55 se vier na frente (com 12 ou 13 dígitos: 55 + DDD + numero)
        if (in_array(strlen($digitos), [12, 13]) && str_starts_with($digitos, '55')) {
            $digitos = substr($digitos, 2);
        }

        return $digitos === '' ? null : $digitos;
    }

    public function normalizarEmail(?string $email): ?string
    {
        return blank($email) ? null : strtolower(trim($email));
    }

    public function resolverContato(string $nome, ?string $telefone, ?string $email, string $portal): Contato
    {
        $telefoneNormalizado = $this->normalizarTelefone($telefone);
        $emailNormalizado = $this->normalizarEmail($email);

        $contato = null;

        if ($telefoneNormalizado) {
            $contato = Contato::where('telefone_normalizado', $telefoneNormalizado)->first();
        }

        if (! $contato && $emailNormalizado) {
            $contato = Contato::where('email_normalizado', $emailNormalizado)->first();
        }

        if (! $contato) {
            $contato = Contato::create([
                'nome' => $nome,
                'telefone_normalizado' => $telefoneNormalizado,
                'email_normalizado' => $emailNormalizado,
            ]);
        }

        LeadOrigem::create([
            'contato_id' => $contato->id,
            'portal' => $portal,
            'recebido_em' => now(),
        ]);

        return $contato;
    }
}
