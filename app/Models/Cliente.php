<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    public function notas(){
        return $this->hasMany(Nota::class);
    }
}
