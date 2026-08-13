<?php

use App\Http\Controllers\Public\ClienteLogoutController;
use App\Http\Controllers\Public\EstoqueController;
use App\Http\Controllers\Public\FeedController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\SitemapController;
use App\Http\Controllers\Public\VeiculoController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Cliente;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Site público (vitrine da revenda)
|--------------------------------------------------------------------------
| Rotas do site público, resolvido por subdomínio (ver ResolveTenant em
| bootstrap/app.php) - cada empresa tem sua própria vitrine no mesmo código.
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/estoque', EstoqueController::class)->name('estoque');
Route::get('/veiculo/{veiculo:slug}', VeiculoController::class)->name('veiculo.show');
Route::get('/feeds/{canal}.csv', FeedController::class)->name('feed');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Área do Cliente (comprador final - guarda "cliente", separada da equipe)
|--------------------------------------------------------------------------
*/
Route::prefix('cliente')->name('cliente.')->group(function () {
    Route::middleware('guest:cliente')->group(function () {
        Route::get('/login', Cliente\Login::class)->name('login');
        Route::get('/cadastro', Cliente\Registrar::class)->name('cadastro');
    });

    Route::post('/logout', ClienteLogoutController::class)->name('logout');

    Route::middleware('auth:cliente')->group(function () {
        Route::get('/', Cliente\Dashboard::class)->name('dashboard');
        Route::get('/favoritos', Cliente\Favoritos::class)->name('favoritos');
        Route::get('/compras', Cliente\Compras::class)->name('compras');
        Route::get('/garantias', Cliente\Garantias::class)->name('garantias');
    });
});

require __DIR__.'/auth.php';
