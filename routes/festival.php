<?php

use App\Http\Controllers\ConviteController;
use App\Http\Controllers\MeuStandController;
use App\Http\Controllers\Painel;
use App\Http\Controllers\PublicoController;
use App\Http\Middleware\Papel;
use Illuminate\Support\Facades\Route;

// App público (PWA) + dados vindos do banco
Route::get('/', [PublicoController::class, 'index'])->name('home');
Route::get('/dados.js', [PublicoController::class, 'dados']);

// Convite do expositor
Route::get('/convite/{token}', [ConviteController::class, 'show'])->name('convite.show');
Route::post('/convite/{token}', [ConviteController::class, 'aceitar'])->name('convite.aceitar')->middleware('throttle:10,1');

// Depois do login: cada papel vai para a sua área
Route::get('/dashboard', function () {
    return redirect(auth()->user()->isAdmin() ? '/painel' : '/meu-stand');
})->middleware('auth')->name('dashboard');

// Expositor
Route::middleware(['auth', 'verified', Papel::class.':expositor'])->prefix('meu-stand')->name('meu-stand.')->group(function () {
    Route::get('/', [MeuStandController::class, 'index'])->name('index');
    Route::get('/{stand}', [MeuStandController::class, 'edit'])->name('edit');
    Route::put('/{stand}', [MeuStandController::class, 'update'])->name('update');
});

// Administrador
Route::middleware(['auth', Papel::class.':admin'])->prefix('painel')->name('painel.')->group(function () {
    Route::get('/', [Painel\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('stands', Painel\StandController::class)->except('show');
    Route::resource('expositores', Painel\ExpositorController::class)->parameters(['expositores' => 'expositor'])->except('show');
    Route::post('expositores/{expositor}/convite', [Painel\ExpositorController::class, 'convite'])->name('expositores.convite');
    Route::post('email-teste', [Painel\DashboardController::class, 'emailTeste'])->middleware('throttle:5,1')->name('email-teste');
    Route::get('alteracoes', [Painel\AlteracaoController::class, 'index'])->name('alteracoes');
    Route::get('mapa', [Painel\MapaController::class, 'edit'])->name('mapa.edit');
    Route::put('mapa', [Painel\MapaController::class, 'update'])->name('mapa.update');

    Route::get('programacao', [Painel\ProgramacaoController::class, 'index'])->name('programacao.index');
    Route::post('programacao/dias', [Painel\ProgramacaoController::class, 'storeDia'])->name('programacao.dia.store');
    Route::put('programacao/dias/{dia}', [Painel\ProgramacaoController::class, 'updateDia'])->name('programacao.dia.update');
    Route::delete('programacao/dias/{dia}', [Painel\ProgramacaoController::class, 'destroyDia'])->name('programacao.dia.destroy');
    Route::post('programacao/dias/{dia}/atracoes', [Painel\ProgramacaoController::class, 'storeAtracao'])->name('programacao.atracao.store');
    Route::put('programacao/atracoes/{atracao}', [Painel\ProgramacaoController::class, 'updateAtracao'])->name('programacao.atracao.update');
    Route::delete('programacao/atracoes/{atracao}', [Painel\ProgramacaoController::class, 'destroyAtracao'])->name('programacao.atracao.destroy');
});
