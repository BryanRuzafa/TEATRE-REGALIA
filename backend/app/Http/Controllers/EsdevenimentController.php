<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Esdeveniment;
use App\Models\Venda;
use App\Models\Seient;

class EsdevenimentController extends Controller
{
    public function index()
    {
        // Llista tots els esdeveniments per a la portada del TR
        return response()->json(Esdeveniment::all());
    }

    public function show($id)
    {
        // Detall d'un esdeveniment amb els seus seients per al mapa en temps real
        $esdeveniment = Esdeveniment::with('seients')->findOrFail($id);
        return response()->json($esdeveniment);
    }

    public function estadistiques()
    {
        // Estadístiques reals per al panell d'administració (Requeriment 3.2.2 i 3.2.3)
        $total_seients = Seient::count();
        $total_venuts = Seient::where('estat', 'venut')->count();
        
        $ocupacio = $total_seients > 0 ? round(($total_venuts / $total_seients) * 100) : 0;

        return response()->json([
            'resum_global' => [
                'total_vendes' => Venda::count(),
                'recaptacio_total' => Venda::sum('preu_final'),
                'ocupacio_mitjana' => $ocupacio . '%',
            ],
            'detall_esdeveniments' => Esdeveniment::withCount('vendes')->get()
        ]);
    }

    public function store(Request $request)
    {
        // Creació de nous esdeveniments des de l'admin (Requeriment 3.2.1)
        $dades = $request->validate([
            'titol' => 'required|string',
            'data_hora' => 'required|date',
            'recinte' => 'required|string',
            'preu_base' => 'required|numeric'
        ]);

        $esdeveniment = Esdeveniment::create($dades);

        // Generem seients automàticament per a la demo (25 seients)
        for ($i = 1; $i <= 25; $i++) {
            $esdeveniment->seients()->create([
                'id_referencia' => strval($i),
                'estat' => 'disponible'
            ]);
        }

        return response()->json($esdeveniment, 201);
    }

    public function update(Request $request, $id)
    {
        // Edició d'esdeveniments (Requeriment 3.2.1)
        $esdeveniment = Esdeveniment::findOrFail($id);
        
        $dades = $request->validate([
            'titol' => 'required|string',
            'data_hora' => 'required|date',
            'recinte' => 'required|string',
            'preu_base' => 'required|numeric'
        ]);

        $esdeveniment->update($dades);

        return response()->json(['exit' => true, 'esdeveniment' => $esdeveniment]);
    }

    public function destroy($id)
    {
        // Eliminació d'esdeveniment (Requeriment de gestió administrativa)
        $esdeveniment = Esdeveniment::findOrFail($id);
        $esdeveniment->delete();

        return response()->json(['exit' => true, 'missatge' => 'Esdeveniment eliminat correctament.']);
    }
}
