<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\LeadRecebido;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\LeadResource;
use App\Models\Lead;
use App\Services\Leads\LeadDistribuidor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LeadController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('leads.ver');

        $leads = Lead::query()
            ->when($request->filled('estagio'), fn ($q) => $q->where('estagio', $request->string('estagio')))
            ->orderByDesc('created_at')
            ->paginate(min((int) $request->integer('por_pagina', 25), 100));

        return LeadResource::collection($leads);
    }

    public function store(Request $request): LeadResource
    {
        $this->authorize('leads.criar');

        $dados = $request->validate([
            'nome' => 'required|string|max:150',
            'telefone' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'veiculo_id' => 'nullable|integer|exists:veiculos,id',
            'mensagem' => 'nullable|string|max:2000',
        ]);

        $lead = Lead::create([
            'veiculo_id' => $dados['veiculo_id'] ?? null,
            'nome' => $dados['nome'],
            'telefone' => $dados['telefone'],
            'email' => $dados['email'] ?? null,
            'mensagem_original' => $dados['mensagem'] ?? null,
            'origem' => 'outro',
            'estagio' => 'novo',
            'ip_origem' => $request->ip(),
        ]);

        (new LeadDistribuidor)->distribuir($lead);
        event(new LeadRecebido($lead));

        return new LeadResource($lead);
    }
}
