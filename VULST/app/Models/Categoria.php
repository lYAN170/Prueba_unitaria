<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias'; 

    protected $fillable = [
        'name',
    ];

    public $timestamps = true;

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
