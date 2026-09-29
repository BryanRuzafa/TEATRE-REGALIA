<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Esdeveniment;
use App\Models\Seient;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Esdeveniments de "vulto" per a la demo del TR (Cicle de Cinema Popular)
        $pelicules = [
            [
                'titol' => 'Torrente: El Braç Tonto de la Llei',
                'descripcio' => 'Projecció del clàssic del cinema de culte espanyol.',
                'data_hora' => now()->addDays(2),
                'recinte' => 'Sala d\'Actes DAW',
                'preu_base' => 12.50,
                'imatge_url' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&q=80&w=800'
            ],
            [
                'titol' => 'Pa Negre (Versió Remasteritzada)',
                'descripcio' => 'Drama històric guanyador de 9 premis Goya.',
                'data_hora' => now()->addDays(3),
                'recinte' => 'Auditori Regalia',
                'preu_base' => 15.00,
                'imatge_url' => 'https://images.unsplash.com/photo-1440404653325-ab127d49abc1?auto=format&fit=crop&q=80&w=800'
            ],
            [
                'titol' => 'Campeones',
                'descripcio' => 'Una comèdia que ens ensenya el valor del treball en equip.',
                'data_hora' => now()->addDays(5),
                'recinte' => 'Sala VIP A3',
                'preu_base' => 10.00,
                'imatge_url' => 'https://images.unsplash.com/photo-1543326175-3b996843624d?auto=format&fit=crop&q=80&w=800'
            ],
            [
                'titol' => 'Lo Imposible',
                'descripcio' => 'Una història de supervivència i coratge de J.A. Bayona.',
                'data_hora' => now()->addDays(10),
                'recinte' => 'Auditori Regalia',
                'preu_base' => 18.00,
                'imatge_url' => 'https://images.unsplash.com/photo-1533619239233-628003c193b7?auto=format&fit=crop&q=80&w=800'
            ]
        ];

        foreach ($pelicules as $p) {
            $esdeveniment = Esdeveniment::create($p);

            // Generem 25 seients per a cada esdeveniment (rectangular 5x5)
            for ($i = 1; $i <= 25; $i++) {
                Seient::create([
                    'esdeveniment_id' => $esdeveniment->id,
                    'id_referencia' => strval($i),
                    'estat' => 'disponible'
                ]);
            }
        }
    }
}
