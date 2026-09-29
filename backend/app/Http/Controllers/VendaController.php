<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venda;
use App\Models\Seient;
use Illuminate\Support\Str;
use App\Models\Esdeveniment;
use Illuminate\Support\Facades\DB;

class VendaController extends Controller
{
    public function store(Request $request)
    {
        // Confirmació de compra (Requeriment 3.1.4)
        $dades = $request->validate([
            'esdeveniment_id' => 'required|exists:esdeveniments,id',
            'client_id' => 'required', // ID de sessió del client
            'nom' => 'required|string',
            'email' => 'required|email',
            'seients' => 'required|array' // Llista de id_referencia
        ]);

        // Verifiquem que els seients estan realment reservats per aquest usuari
        // En un TR, aquesta validació és clau per a la nota de concurrència i seguretat
        $seients_db = Seient::where('esdeveniment_id', $dades['esdeveniment_id'])
            ->whereIn('id_referencia', $dades['seients'])
            ->where('reservat_per', $dades['client_id'])
            ->where('estat', 'reservat')
            ->get();

        if ($seients_db->count() !== count($dades['seients'])) {
            return response()->json([
                'exit' => false, 
                'error' => 'Alguns seients han expirat o no t\'hi pertanyen.'
            ], 400);
        }

        // Obtenim el preu de l'esdeveniment per a la venda
        $esdeveniment = Esdeveniment::findOrFail($dades['esdeveniment_id']);
        $codi = strtoupper(Str::random(8));

        $vendes_creades = [];

        DB::transaction(function () use ($seients_db, $dades, $esdeveniment, $codi, &$vendes_creades) {
            foreach($seients_db as $s) {
                // Marquem com a venut i netegem la reserva temporal
                $s->update([
                    'estat' => 'venut',
                    'reservat_per' => null,
                    'temps_bloqueig_finalitza' => null
                ]);

                // Creem el tiquet individual
                $vendes_creades[] = Venda::create([
                    'esdeveniment_id' => $dades['esdeveniment_id'],
                    'seient_id' => $s->id,
                    'nom_comprador' => $dades['nom'],
                    'email_comprador' => $dades['email'],
                    'preu_final' => $esdeveniment->preu_base,
                    'codi_confirmacio' => $codi
                ]);
            }
        });

        // Enviament de correu (Requeriment 3.1.4)
        // Protegit amb try-catch: si el correu falla, la compra ja està guardada a la BD
        try {
            $vendes_completes = Venda::with(['esdeveniment', 'seient'])
                ->where('codi_confirmacio', $codi)
                ->get();
            \Illuminate\Support\Facades\Mail::to($dades['email'])->send(new \App\Mail\ConfirmacioCompra($vendes_completes, $codi));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error enviant correu de confirmacio: ' . $e->getMessage());
        }

        return response()->json([
            'exit' => true,
            'codi_confirmacio' => $codi
        ]);
    }

    public function showByEmail($email)
    {
        // Consulta d'entrades comprades (Requeriment 3.1.5)
        $vendes = Venda::with(['esdeveniment', 'seient'])
            ->where('email_comprador', $email)
            ->get();
            
        return response()->json($vendes);
    }
}
