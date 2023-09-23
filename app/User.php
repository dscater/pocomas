<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'password', 'tipo', 'foto', 'estado', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function datosUsuario()
    {
        return $this->hasOne('App\DatosUsuario', 'user_id', 'id');
    }

    public function caja()
    {
        return $this->hasOne(UserCaja::class, 'user_id');
    }

    public function inicio_cajas()
    {
        return $this->hasMany(InicioCaja::class, 'user_id');
    }

    public function cierre_cajas()
    {
        return $this->hasMany(CierreCaja::class, 'user_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'user_id');
    }

    public function sesion_user()
    {
        return $this->hasOne(SesionUser::class, 'user_id');
    }
}
