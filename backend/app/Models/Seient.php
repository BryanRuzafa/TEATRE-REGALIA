<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seient extends Model
{
    use HasFactory;

    protected $table = 'seients';

    protected $fillable = [
        'esdeveniment_id',
        'id_referencia',
        'estat',
        'reservat_per',
        'temps_bloqueig_finalitza'
    ];

    public function esdeveniment()
    {
        return $this->belongsTo(Esdeveniment::class);
    }
}
