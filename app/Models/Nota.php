<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    public function movimientos(){
        return $this->belongsToMany(Almacen::class, "movimientos")
                ->withTimestamps()
                ->withPivot(["producto_id","cantidad","tipo_movimiento","precio_unitario_compra", "precio_unitario_venta", "observaciones"]);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }
}
