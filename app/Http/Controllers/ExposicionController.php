<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Galeria;
use App\GaleriaImagen;

class ExposicionController extends Controller
{
    public function exposicion()
    {
        $galeria = Galeria::first();
        $imagenes = GaleriaImagen::where('galeria_id', $galeria->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $tipo = $galeria->tipo;
        $html = '';
        if ($tipo == '1X2') {
            $html .= '<li class="c12">';
            $contador_img = 0;
            $cont = 0;
            foreach ($imagenes as $img) {
                $html .= '
                    <img src="' . asset('imgs/galerias/' . $img->imagen) . '" alt="Imagen">
                ';
                $contador_img++;
                $cont++;
                if ($contador_img == 2 && $cont < count($imagenes)) {
                    $html .= '</li>';
                    $html .= '<li class="c12">';
                    $contador_img = 0;
                }
            }
            $html .= '</li>';
        } else {
            $html .= '<li class="c24">';
            $contador_img = 0;
            $cont = 0;
            foreach ($imagenes as $img) {
                $html .= '
                    <img src="' . asset('imgs/galerias/' . $img->imagen) . '" alt="Imagen">
                ';
                $contador_img++;
                $cont++;
                if ($contador_img == 4 && $cont < count($imagenes)) {
                    $html .= '</li>';
                    $html .= '<li class="c24">';
                    $contador_img = 0;
                }
            }

            $html .= '</li>';
        }

        return view('exposicion', compact('html'));
    }
}
