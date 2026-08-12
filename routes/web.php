<?php

use App\Http\Controllers\Public\EstoqueController;
use App\Http\Controllers\Public\FeedController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\VeiculoController;
use App\Http\Controllers\ProfileController;
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
