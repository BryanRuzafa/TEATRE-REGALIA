<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seient;
use App\Events\SeientActualitzat;

class SeientController extends Controller
{
    public function index() {
        $seients = Seient::all()->keyBy('id_referencia');
        return response()->json(['seients' => $seients]);
    }

    public function reservaTemporal(Request $request) {
        $client_id = $request->client_id;
        $id_seient = $request->id_seient;
        $esdeveniment_id = $request->esdeveniment_id;

        // Marcant el seient a la DB SQL per aturar qualsevol compra paral·lela
        $afectats = Seient::where('id_referencia', $id_seient)
            ->where('esdeveniment_id', $esdeveniment_id)
            ->where('estat', 'disponible')
            ->update([
                'estat' => 'reservat',
                'reservat_per' => $client_id,
                'temps_bloqueig_finalitza' => now()->addSeconds(180) // 3 minuts (TR)
            ]);

        if ($afectats > 0) {
            return response()->json(['exit' => true]);
        }
        
        return response()->json(['missatge' => 'Seient ja ocupat a la base de dades'], 409);
    }

    public function alliberarReserva(Request $request) {
        $id_seient = $request->id_seient;
        $esdeveniment_id = $request->esdeveniment_id;

        Seient::where('id_referencia', $id_seient)
            ->where('esdeveniment_id', $esdeveniment_id)
            ->where('estat', 'reservat')
            ->update([
                'estat' => 'disponible',
                'reservat_per' => null,
                'temps_bloqueig_finalitza' => null
            ]);

        return response()->json(['exit' => true]);
    }

    public function confirmarCompra(Request $request) {
        $client_id = $request->client_id;
        
        $seients_modificats = Seient::where('reservat_per', $client_id)
            ->where('estat', 'reservat')
            ->where('temps_bloqueig_finalitza', '>', now())
            ->get();
            
        if ($seients_modificats->isEmpty()) {
            return response()->json(['error' => 'No tens entrades vàlides pendents de facturar o han caducat (Timeout)'], 400);
        }

        Seient::whereIn('id', $seients_modificats->pluck('id'))->update(['estat' => 'venut']);

        foreach($seients_modificats as $seient) {
            $seient->estat = 'venut';
            broadcast(new SeientActualitzat($seient));
        }

        return response()->json(['status' => 'confirmat']);
    }
}
