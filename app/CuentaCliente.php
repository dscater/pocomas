<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CuentaCliente extends Model
{
    protected $fillable = [
        "cliente_id",
        "total_deuda",
        "cancelado",
        "saldo",
        "estado",
    ];

    protected $appends = ["ultimo_pago"];

    public function getUltimoPagoAttribute()
    {
        $ultimo_pago = CuentaPago::where("cuenta_id", $this->id)->get()->last();

        return $ultimo_pago;
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function cuenta_pagos()
    {
        return $this->hasMany(CuentaPago::class, 'cuenta_id');
    }

    public function cuenta_cobrars()
    {
        return $this->hasMany(CuentaCobrar::class, 'cuenta_id');
    }
}
