<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\VendaResource;
use App\Models\Venda;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VendaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('vendas.ver');

        $vendas = Venda::query()
            ->orderByDesc('created_at')
            ->paginate(min((int) $request->integer('por_pagina', 25), 100));

        return VendaResource::collection($vendas);
    }
}
