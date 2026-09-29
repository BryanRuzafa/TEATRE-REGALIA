<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    protected $table = 'vendes';

    protected $fillable = [
        'esdeveniment_id',
        'seient_id',
        'nom_comprador',
        'email_comprador',
        'preu_final',
        'codi_confirmacio'
    ];

    public function esdeveniment()
    {
        return $this->belongsTo(Esdeveniment::class);
    }

    public function seient()
    {
        return $this->belongsTo(Seient::class);
    }
}
