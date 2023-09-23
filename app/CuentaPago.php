<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CuentaPago extends Model
{
    protected $fillable = [
        'cuenta_id', 'caja_id', 'monto',
        'observacion', 'tipo_cobro', 'fecha_pago',
    ];

    public function cuenta_cliente()
    {
        return $this->belongsTo(CuentaCliente::class, 'cuenta_id');
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }
}
