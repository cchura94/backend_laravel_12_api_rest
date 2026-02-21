<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{

    protected $table = 'clientes';

    protected $fillable = [
        'tipo',
        'razon_social',
        'nro_identificacion',
        'telefono',
        'direccion',
        'estado'
    ];

    public function notas(){
        return $this->hasMany(Nota::class);
    }
}
