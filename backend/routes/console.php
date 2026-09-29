<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Seient;
use App\Events\SeientActualitzat;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Substitut automàtic Timeout Backend a intervals per comprovar neteges asíncrones
Schedule::call(function () {
    $caducats = Seient::where('estat', 'reservat')
        ->where('temps_bloqueig_finalitza', '<', now())
        ->get();
    
    if ($caducats->isNotEmpty()) {
        Seient::whereIn('id', $caducats->pluck('id'))->update(['estat' => 'disponible', 'reservat_per' => null]);
        foreach($caducats as $s) {
            $s->estat = 'disponible';
            $s->reservat_per = null;
            broadcast(new SeientActualitzat($s));
        }
    }
})->everySecond();
