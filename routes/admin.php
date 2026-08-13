<?php

use App\Http\Controllers\Admin\ContratoPdfController;
use App\Livewire\Admin\Upgrade;
use App\Livewire\Anuncios;
use App\Livewire\Clientes;
use App\Livewire\Configuracoes;
use App\Livewire\Contratos;
use App\Livewire\Crm;
use App\Livewire\Dashboard;
use App\Livewire\Financeiro;
use App\Livewire\Fornecedores;
use App\Livewire\Importacoes;
use App\Livewire\Leads;
use App\Livewire\Relatorios;
use App\Livewire\Usuarios;
use App\Livewire\Veiculos;
use App\Livewire\Vendas;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Prefixo /admin, escopado por tenant (via ResolveTenant, ver bootstrap/app.php)
| e por permissão (Spatie, ver config/permission.php). Rotas de cada módulo
| são adicionadas aqui conforme a Fase 1 avança (ver plano: Estoque, CRM,
| Contratos, Financeiro, Anúncios, Dashboard...).
|
| A rota /admin/dashboard mantém o NOME "dashboard" (sem prefixo admin.) de
| propósito - os controllers de auth do Breeze têm route('dashboard') fixo
| em 7 lugares, e é mais simples manter esse nome do que editar tudo isso.
|
| Rotas de módulos fora do plano "somente_site" (ver Empresa::MODULOS_POR_PLANO
| e prompt §4.16/§5) levam ->middleware('modulo:slug') - bloqueia com tela de
| upgrade quando a empresa não tem acesso aquele módulo.
*/

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::name('admin.')->group(function () {
        Route::get('/upgrade', Upgrade::class)->name('upgrade');

        Route::get('/veiculos', Veiculos\Index::class)->name('veiculos.index');
        Route::get('/veiculos/novo', Veiculos\Form::class)->name('veiculos.novo');
        Route::get('/veiculos/{veiculo}/editar', Veiculos\Form::class)->name('veiculos.editar');

        Route::get('/crm', Crm\Pipeline::class)->name('crm.pipeline');
        Route::get('/leads', Leads\Inbox::class)->name('leads.inbox');

        Route::get('/configuracoes', Configuracoes\Index::class)->name('configuracoes.index');

        Route::middleware('modulo:clientes')->group(function () {
            Route::get('/clientes', Clientes\Index::class)->name('clientes.index');
        });

        Route::middleware('modulo:fornecedores')->group(function () {
            Route::get('/fornecedores', Fornecedores\Index::class)->name('fornecedores.index');
        });

        Route::middleware('modulo:importacoes')->group(function () {
            Route::get('/importacoes', Importacoes\Wizard::class)->name('importacoes.index');
        });

        Route::middleware('modulo:vendas')->group(function () {
            Route::get('/vendas', Vendas\Index::class)->name('vendas.index');
            Route::get('/vendas/nova', Vendas\Nova::class)->name('vendas.nova');
        });

        Route::middleware('modulo:contratos')->group(function () {
            Route::get('/contratos', Contratos\Index::class)->name('contratos.index');
            Route::get('/contratos/gerar/{venda}', Contratos\Gerar::class)->name('contratos.gerar');
            Route::get('/contratos/{contrato}/pdf', ContratoPdfController::class)->name('contratos.pdf');
        });

        Route::prefix('financeiro')->name('financeiro.')->middleware('modulo:financeiro')->group(function () {
            Route::get('/', Financeiro\ContasPagar::class)->name('index');
            Route::get('/receber', Financeiro\ContasReceber::class)->name('receber');
            Route::get('/dre', Financeiro\Dre::class)->name('dre');
            Route::get('/conciliacao', Financeiro\Conciliacao::class)->name('conciliacao');
        });

        Route::middleware('modulo:anuncios')->group(function () {
            Route::get('/anuncios', Anuncios\Index::class)->name('anuncios.index');
        });

        Route::middleware('modulo:relatorios')->group(function () {
            Route::get('/relatorios', Relatorios\Estoque::class)->name('relatorios.index');
        });

        Route::middleware('modulo:usuarios')->group(function () {
            Route::get('/usuarios', Usuarios\Index::class)->name('usuarios.index');
        });
    });
});
