<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Esdeveniment extends Model
{
    use HasFactory;

    protected $fillable = [
        'titol',
        'descripcio',
        'data_hora',
        'recinte',
        'imatge_url',
        'preu_base'
    ];

    public function seients()
    {
        return $this->hasMany(Seient::class);
    }

    public function vendes()
    {
        return $this->hasMany(Venda::class);
    }
}
