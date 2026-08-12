<?php

namespace App\Services\Leads;

use App\Models\BloqueioLead;

/**
 * Roda antes do lead entrar no funil (ver plano §8) - nunca deleta
 * automaticamente, só marca lead_falso=true com motivo, o vendedor confirma
 * ou reverte na tela do lead.
 */
class LeadFalsoDetector
{
    /** DDDs válidos no Brasil (ANATEL). */
    protected const DDDS_VALIDOS = [
        11, 12, 13, 14, 15, 16, 17, 18, 19,
        21, 22, 24, 27, 28,
        31, 32, 33, 34, 35, 37, 38,
        41, 42, 43, 44, 45, 46, 47, 48, 49,
        51, 53, 54, 55,
        61, 62, 63, 64, 65, 66, 67, 68, 69,
        71, 73, 74, 75, 77, 79,
        81, 82, 83, 84, 85, 86, 87, 88, 89,
        91, 92, 93, 94, 95, 96, 97, 98, 99,
    ];

    public function detectar(?string $telefoneNormalizado, ?string $emailNormalizado): ?string
    {
        if ($telefoneNormalizado) {
            if ($motivo = $this->verificarBloqueio('telefone', $telefoneNormalizado)) {
                return $motivo;
            }

            if (! $this->dddValido($telefoneNormalizado)) {
                return 'DDD inválido';
            }

            if ($this->padraoRepetido($telefoneNormalizado)) {
                return 'Número com padrão repetido (ex: 11111111111)';
            }
        }

        if ($emailNormalizado && $this->verificarBloqueio('email', $emailNormalizado)) {
            return 'E-mail na lista de bloqueio';
        }

        return null;
    }

    protected function dddValido(string $telefoneNormalizado): bool
    {
        // Telefone só com DDD+número (sem DDI) tem 10 (fixo) ou 11 (celular) dígitos.
        if (! in_array(strlen($telefoneNormalizado), [10, 11])) {
            return false;
        }

        $ddd = (int) substr($telefoneNormalizado, 0, 2);

        return in_array($ddd, self::DDDS_VALIDOS, true);
    }

    protected function padraoRepetido(string $telefoneNormalizado): bool
    {
        // Todos os dígitos iguais (11111111111) ou sequência óbvia (12345678900).
        if (preg_match('/^(\d)\1+$/', $telefoneNormalizado)) {
            return true;
        }

        $numero = substr($telefoneNormalizado, 2);

        return $numero === '123456789' || $numero === '987654321';
    }

    protected function verificarBloqueio(string $tipo, string $valor): ?string
    {
        $bloqueio = BloqueioLead::where('tipo', $tipo)->where('valor', $valor)->first();

        return $bloqueio?->motivo ?? ($bloqueio ? 'Bloqueado manualmente' : null);
    }
}
