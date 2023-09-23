<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DatosUsuario extends Model
{
    protected $table = 'datos_usuarios';
    protected $fillable = [
        'nombre', 'paterno', 'materno', 'ci',
        'ci_exp', 'sexo', 'dir', 'fono',
        'cel', 'email', 'fecha_ingreso', 'user_id',
    ];


    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
