<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CuentaPagarDetalle extends Model
{
    protected $fillable = [
        "cuenta_pagar_id", "monto", 'tipo_pago', "saldo", "total", "descripcion", "fecha"
    ];

    public function cuenta_pagar()
    {
        return $this->belongsTo(CuentaPagar::class, 'cuenta_pagar_id');
    }
}
