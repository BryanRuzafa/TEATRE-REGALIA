<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Seient;

class SeientActualitzat implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;
    public $estat;
    public $reservat_per;

    public function __construct(Seient $seient)
    {
        $this->id = $seient->id_referencia;
        $this->estat = $seient->estat;
        $this->reservat_per = $seient->reservat_per;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('sala.a3'),
        ];
    }
}
