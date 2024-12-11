<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    public function persona(){
        return $this->hasMany(Persona::class);
    }

    public function personaa(){
        return $this->hasMany(Personaa::class);
    }
}
