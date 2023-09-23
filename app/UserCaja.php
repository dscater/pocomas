<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCaja extends Model
{
    protected $fillable = [
        'user_id', 'caja_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja');
    }
}
