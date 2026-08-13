<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClienteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('clientes.ver');

        $clientes = Cliente::query()
            ->orderByDesc('created_at')
            ->paginate(min((int) $request->integer('por_pagina', 25), 100));

        return ClienteResource::collection($clientes);
    }

    public function show(Request $request, Cliente $cliente): ClienteResource
    {
        $this->authorize('clientes.ver');

        return new ClienteResource($cliente);
    }
}
