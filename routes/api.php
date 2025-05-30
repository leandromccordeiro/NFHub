<?php

use App\Http\Controllers\Api\NotaFiscalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rotas da API para Notas Fiscais
Route::prefix('notas-fiscais')->name('notas-fiscais.')->group(function () {
    Route::get('/', [NotaFiscalController::class, 'index'])->name('index');
    Route::post('/', [NotaFiscalController::class, 'store'])->name('store');
    Route::get('/estatisticas', [NotaFiscalController::class, 'estatisticas'])->name('estatisticas');
    Route::get('/{notaFiscal}', [NotaFiscalController::class, 'show'])->name('show');
    Route::put('/{notaFiscal}', [NotaFiscalController::class, 'update'])->name('update');
    Route::delete('/{notaFiscal}', [NotaFiscalController::class, 'destroy'])->name('destroy');
    Route::get('/{notaFiscal}/download', [NotaFiscalController::class, 'download'])->name('download');
}); 