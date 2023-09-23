<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CierreCaja extends Model
{
    protected $fillable = [
        'caja_id', 'inicio_caja_id', 'monto_total', 'fecha_cierre',
        'descripcion', 'user_id', 'fecha_registro',
        'estado',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public static function existeCierre($fecha)
    {
        $existe = CierreCaja::where('fecha_cierre', $fecha)
            ->where('estado', 1)->get()->last();
        if ($existe) {
            return $existe;
        }
        return false;
    }
}
