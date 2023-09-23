<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $fillable = [
        'venta_id', 'nro_factura', 'cliente', 'nit',
    ];
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}
