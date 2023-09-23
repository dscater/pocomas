<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SesionUser extends Model
{
    protected $fillable = [
        "user_id",
        "navegador",
        "dispositivo",
        "sistema",
        "detalle",
        "estado",
    ];
}
