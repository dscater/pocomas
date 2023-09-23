<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nombre', 'ci', 'razon_social', 'email', 'celular',
        'fecha_registro', 'estado',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cliente_id');
    }

    public function cuenta_cliente()
    {
        return $this->hasOne(CuentaCliente::class, 'cliente_id');
    }
}
