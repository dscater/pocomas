<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\RazonSocial;
class RazonSocialController extends Controller
{
    public function index()
    {
        $razon_social = RazonSocial::first(); 
        return view('razon_social.index',compact('razon_social'));   
    }

    public function edit(RazonSocial $razon_social)
    {
        return view('razon_social.edit',compact('razon_social'));   
    }

    public function update(RazonSocial $razon_social, Request $request)
    {
        $razon_social->update(array_map('mb_strtoupper',$request->except('logo')));

        if($request->hasFile('logo'))
        {
            // antiguo
            $antiguo = $razon_social->logo;
            \File::delete(public_path().'/imgs/'.$antiguo);

            //obtener el archivo
            $file_logo = $request->file('logo');
            $extension = ".".$file_logo->getClientOriginalExtension();
            $nom_logo = 'logo'.time().$extension;
            $file_logo->move(public_path()."/imgs/",$nom_logo);
            $razon_social->logo = $nom_logo;
        }
        $razon_social->save();

        return redirect()->route('razon_social.index')->with('bien','Modificación realizada con éxito');
    }
}
