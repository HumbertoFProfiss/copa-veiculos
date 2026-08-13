<?php

use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\LeadController;
use App\Http\Controllers\Api\V1\VeiculoController;
use App\Http\Controllers\Api\V1\VendaController;
use Illuminate\Support\Facades\Route;

/**
 * API REST v1 - autenticacao via token Sanctum (gerado em
 * /admin/integracoes), escopada automaticamente por empresa via
 * BelongsToEmpresa (tenant resolvido pelo Host, ver bootstrap/app.php).
 * Cobertura deliberadamente enxuta (leitura + criacao de lead, os casos de
 * uso reais de integracao externa) em vez de CRUD completo de tudo.
 */
Route::prefix('v1')->middleware('auth:sanctum')->name('api.v1.')->group(function () {
    Route::get('veiculos', [VeiculoController::class, 'index'])->name('veiculos.index');
    Route::get('veiculos/{veiculo}', [VeiculoController::class, 'show'])->name('veiculos.show');

    Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');

    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::post('leads', [LeadController::class, 'store'])->name('leads.store');

    Route::get('vendas', [VendaController::class, 'index'])->name('vendas.index');
});
