<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EsdevenimentController;
use App\Http\Controllers\VendaController;
use App\Http\Controllers\SeientController;

// Endpoints en Català segons normativa del TR
// Endpoint de verificació de salut de l'API
Route::get('/salut', function() {
    try {
        DB::connection()->getPdo();
        return response()->json(['estat' => 'correcte', 'base_dades' => 'connectada']);
    } catch (\Exception $e) {
        return response()->json(['estat' => 'error', 'missatge' => $e->getMessage()], 500);
    }
});

Route::get('/esdeveniments', [EsdevenimentController::class, 'index']);
Route::get('/esdeveniments/{id}', [EsdevenimentController::class, 'show']);

Route::post('/venda/confirmar', [VendaController::class, 'store']);
Route::get('/venda/consulta/{email}', [VendaController::class, 'showByEmail']);

// Sincronització interna Sockets -> Laravel
Route::post('/seients/reservar', [SeientController::class, 'reservaTemporal']);
Route::post('/seients/alliberar', [SeientController::class, 'alliberarReserva']);

// L'administració (Simple per al TR)
Route::prefix('admin')->group(function() {
    Route::get('/estadistiques', [EsdevenimentController::class, 'estadistiques']);
    Route::post('/esdeveniments', [EsdevenimentController::class, 'store']);
    Route::put('/esdeveniments/{id}', [EsdevenimentController::class, 'update']);
    Route::delete('/esdeveniments/{id}', [EsdevenimentController::class, 'destroy']);
});
