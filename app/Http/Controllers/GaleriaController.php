<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Galeria;
use App\GaleriaImagen;

class GaleriaController extends Controller
{
    public function index()
    {
        $galeria = Galeria::first();
        $imagenes = GaleriaImagen::where('galeria_id', $galeria->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('galerias.index', compact('galeria', 'imagenes'));
    }

    public function update(Galeria $galeria, Request $request)
    {
        $galeria->update(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('galerias.index')->with('bien', 'Registro modificado con éxito');
    }

    public function store(Request $request)
    {
        $galeria = Galeria::find($request->galeria_id);
        if ($request->hasFile('file')) {
            //obtener el archivo
            $file = $request->file('file');
            $extension = "." . $file->getClientOriginalExtension();
            $nom_file = \str_replace(' ', '_', $galeria->nombre) . '_' . time() . $extension;
            $file->move(public_path() . "/imgs/galerias/", $nom_file);

            GaleriaImagen::create([
                'galeria_id' => $galeria->id,
                'imagen' => $nom_file,
                'fecha_registro' => date('Y-m-d')
            ]);
        }

        $imagenes = GaleriaImagen::where('galeria_id', $galeria->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $html = view('galerias.parcial.imgs', compact('imagenes'))->render();
        return response()->JSON([
            'sw' => true,
            'html' => $html
        ]);
    }

    public function getImgs(Galeria $galeria)
    {
        $imagenes = GaleriaImagen::where('galeria_id', $galeria->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $html = view('galerias.parcial.imgs', compact('imagenes'))->render();
        return response()->JSON([
            'sw' => true,
            'html' => $html
        ]);
    }

    public function destroy(GaleriaImagen $galeria_imagen)
    {
        \File::delete(public_path() . '/imgs/galerias/' . $galeria_imagen->imagen);
        $galeria_imagen->delete();
        return redirect()->route('galerias.index')->with('bien', 'Imágen eliminada con éxito');
    }
}
