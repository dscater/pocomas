<?php

use Illuminate\Database\Seeder;

use App\Galeria;

class GaleriaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Galeria::create([
            'nombre' => 'GALERÍA',
            'tipo' => '1X2',
            'fecha_registro' => date('Y-m-d')
        ]);
    }
}
