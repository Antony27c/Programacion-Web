<?php

use App\Http\Controllers\Api\ServicioApiController;
use App\Http\Controllers\ServicioController;
use Illuminate\Support\Facades\Route;

// Inicio: redirige al catálogo de servicios
Route::get('/', fn () => redirect()->route('servicios.index'));

// CRUD del catálogo de servicios (index, create, store, show, edit, update, destroy)
Route::resource('servicios', ServicioController::class);

// API JSON de solo lectura
Route::prefix('api')->name('api.')->group(function () {
    Route::get('servicios', [ServicioApiController::class, 'index'])->name('servicios.index');
    Route::get('servicios/{id}', [ServicioApiController::class, 'show'])->name('servicios.show');
});
