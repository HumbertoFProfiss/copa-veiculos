<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\VeiculoResource;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VeiculoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('veiculos.ver');

        $veiculos = Veiculo::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderByDesc('created_at')
            ->paginate(min((int) $request->integer('por_pagina', 25), 100));

        return VeiculoResource::collection($veiculos);
    }

    public function show(Request $request, Veiculo $veiculo): VeiculoResource
    {
        $this->authorize('veiculos.ver');

        return new VeiculoResource($veiculo);
    }
}
