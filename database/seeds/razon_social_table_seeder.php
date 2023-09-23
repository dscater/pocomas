<?php

use Illuminate\Database\Seeder;

use App\RazonSocial;

class razon_social_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RazonSocial::create([
            'nombre' => 'EMPRESA PRUEBA',
            'alias' => 'EP',
            'nombre_propietario' => 'JUAN PEREZ',
            'pais' => 'BOLIVIA',
            'ciudad' => 'LA PAZ',
            'dir' => 'ZONA LOS OLIVOS CALLE 3 #3232',
            'nit' => '100000111111',
            'nro_aut' => '1000001555',
            'fono' => '21134568',
            'cel' => '78945612',
            'correo' => '',
            'logo' => 'logo.png',
            'actividad_economica' => 'ACTIVIDAD ECONOMICA',
            'fecha_registro' => date('Y-m-d')
        ]);
    }
}
