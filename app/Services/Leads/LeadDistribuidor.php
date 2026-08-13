<?php

namespace App\Services\Leads;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Distribui leads automaticamente entre vendedores por rodizio (ver prompt
 * §4.7 - "Distribuicao automatica de leads entre vendedores (rodizio ou por
 * regra)"). Escolhe sempre quem esta a mais tempo sem receber um lead novo,
 * entre os usuarios ativos com papel Vendedor da empresa atual.
 */
class LeadDistribuidor
{
    public function distribuir(Lead $lead): void
    {
        if ($lead->vendedor_id) {
            return;
        }

        $vendedores = User::role('Vendedor')->where('ativo', true)->get(['id']);

        if ($vendedores->isEmpty()) {
            return;
        }

        $idsVendedores = $vendedores->pluck('id');

        // MAX(id) em vez de MAX(created_at): timestamp so tem precisao de
        // segundo, entao 2+ leads chegando no mesmo segundo empatariam e
        // quebrariam o rodizio. O id do lead e sempre estritamente
        // crescente, entao nunca empata.
        $ultimoLeadIdPorVendedor = Lead::whereIn('vendedor_id', $idsVendedores)
            ->select('vendedor_id', DB::raw('MAX(id) as ultimo_lead_id'))
            ->groupBy('vendedor_id')
            ->pluck('ultimo_lead_id', 'vendedor_id');

        $escolhido = $idsVendedores
            ->sortBy(fn (int $id) => $ultimoLeadIdPorVendedor[$id] ?? 0)
            ->first();

        $lead->update(['vendedor_id' => $escolhido]);
    }
}
