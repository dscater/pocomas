<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InicioCaja extends Model
{
    protected $fillable = [
        'caja_id', 'monto_inicial', 'fecha_inicio',
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

    public static function existeInicio($fecha, $id = 0)
    {
        if ($id != 0) {
            // $existe = InicioCaja::where('fecha_inicio', $fecha)
            //     ->where('estado', 1)
            //     ->where('caja_id', $id)
            //     ->get()->last();

            $existe = InicioCaja::where('estado', 1)
                ->where('caja_id', $id)
                ->get()->last();
        } else {
            $existe = InicioCaja::where('estado', 1)->get()->last();
        }
        if ($existe) {
            return $existe;
        }
        return false;
    }
}
