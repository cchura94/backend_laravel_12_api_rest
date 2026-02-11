<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    public function categoria(){
        return $this->belongsTo(Categoria::class);
    }

    public function almacences(){
        return $this->belongsToMany(Almacen::class)
                    ->withPivot(["cantidad_actual"])
                    ->withTimestamps();
    }
}
