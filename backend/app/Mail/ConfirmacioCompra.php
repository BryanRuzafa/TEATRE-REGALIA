<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Venda;

class ConfirmacioCompra extends Mailable
{
    use Queueable, SerializesModels;

    public $vendes;
    public $codi;

    public function __construct($vendes, $codi)
    {
        $this->vendes = $vendes;
        $this->codi = $codi;
    }

    public function build()
    {
        return $this->subject('Confirmació de Compra - Teatre TR')
                    ->view('emails.confirmacio');
    }
}
