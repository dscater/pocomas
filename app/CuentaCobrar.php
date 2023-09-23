<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CuentaCobrar extends Model
{
    protected $fillable = [
        'cuenta_id', 'venta_id', 'cliente_id', 'monto_deuda', 'saldo',
        'estado', 'status'
    ];
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function cuenta_cliente()
    {
        return $this->belongsTo(CuentaCliente::class, 'cuenta_id');
    }

    public function cuenta_cobrar_detalles()
    {
        return $this->hasMany(CuentaCobrarDetalle::class, 'cuenta_cobrar_id');
    }
}
