<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserCaja;
use App\DatosUsuario;
use App\Caja;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = DatosUsuario::select('datos_usuarios.*')
            ->join('users', 'users.id', '=', 'datos_usuarios.user_id')
            ->where('users.status', 1)
            ->get();
        return view('users.index', compact('usuarios'));
    }

    public function create()
    {
        $cajas = Caja::where('estado', 1)->get();
        $array_cajas[''] = "Seleccione...";
        foreach ($cajas as $value) {
            $array_cajas[$value->id] =  $value->nombre;
        }
        return view('users.create', compact('array_cajas'));
    }

    public function inactividad(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->session_user) {
            $user->sesion_user->update(["estado" => 0]);
            $user->sesion_user->update(["estado" => 1]);
            return response()->JSON(true);
        }
        return response()->JSON(false);
    }


    public function store(UserStoreRequest $request)
    {
        $usuario = new DatosUsuario(array_map('mb_strtoupper', $request->all()));
        $nombre_usuario = UserController::nombreUsuario($request->nombre, $request->paterno);

        $comprueba = User::where('name', $nombre_usuario)->get()->first();
        $cont = 1;
        while ($comprueba) {
            $nombre_usuario = $nombre_usuario . $cont;
            $comprueba = User::where('name', $nombre_usuario)->get()->first();
            $cont++;
        }

        $nuevo_usuario = new User();
        $nuevo_usuario->name = $nombre_usuario;
        $nuevo_usuario->password = Hash::make($request->ci);
        $nuevo_usuario->tipo = $request->tipo;
        $nuevo_usuario->foto = 'user_default.png';
        $nuevo_usuario->estado = $request->estado;
        $nuevo_usuario->status = 1;
        if ($request->hasFile('foto')) {
            //obtener el archivo
            $file_foto = $request->file('foto');
            $extension = "." . $file_foto->getClientOriginalExtension();
            $nom_foto = $usuario->nombre . time() . $extension;
            $file_foto->move(public_path() . "/imgs/users/", $nom_foto);
            $nuevo_usuario->foto = $nom_foto;
        }
        $nuevo_usuario->save();
        $nuevo_usuario->datosUsuario()->save($usuario);

        if ($nuevo_usuario->tipo == 'CAJA') {
            UserCaja::create([
                'user_id' => $nuevo_usuario->id,
                'caja_id' => $request->caja_id
            ]);
        }

        return redirect()->route('users.index')->with('bien', 'Usuario registrado con éxito');
    }

    public function edit(DatosUsuario $usuario)
    {
        $cajas = Caja::where('estado', 1)->get();
        $array_cajas[''] = "Seleccione...";
        foreach ($cajas as $value) {
            $array_cajas[$value->id] =  $value->nombre;
        }
        return view('users.edit', compact('usuario', 'array_cajas'));
    }

    public function update(DatosUsuario $usuario, UserUpdateRequest $request)
    {
        $usuario->update(array_map('mb_strtoupper', $request->except('foto')));
        $usuario->user->tipo = $request->tipo;
        $usuario->user->estado = $request->estado;
        if ($request->hasFile('foto')) {
            // antiguo
            $antiguo = $usuario->user->foto;
            if ($antiguo != 'user_default.png') {
                \File::delete(public_path() . '/imgs/users/' . $antiguo);
            }

            //obtener el archivo
            $file_foto = $request->file('foto');
            $extension = "." . $file_foto->getClientOriginalExtension();
            $nom_foto = $usuario->nombre . time() . $extension;
            $file_foto->move(public_path() . "/imgs/users/", $nom_foto);
            $usuario->user->foto = $nom_foto;
        }
        $usuario->user->save();

        $existe_caja = UserCaja::where('user_id', $usuario->user->id)->get()->first();
        if ($usuario->user->tipo == 'CAJA') {
            if ($existe_caja) {
                $existe_caja->caja_id = $request->caja_id;
                $existe_caja->save();
            } else {
                UserCaja::create([
                    'user_id' => $usuario->user->id,
                    'caja_id' => $request->caja_id
                ]);
            }
        } else {
            if ($existe_caja) {
                $existe_caja->delete();
            }
        }

        return redirect()->route('users.index')->with('bien', 'Usuario modificado con éxito');
    }

    public function show(DatosUsuario $usuario)
    {
        return 'mostrar usuario';
    }

    public function destroy(User $user)
    {
        $user->status = 0;
        $user->save();
        return redirect()->route('users.index')->with('bien', 'Registro eliminado correctamente');
    }


    public static function nombreUsuario($nom, $apep)
    {
        //determinando el nombre de usuario inicial del 1er_nombre+apep+tipoUser
        $nombre_user = substr(mb_strtoupper($nom), 0, 1); //inicial 1er_nombre
        $nombre_user .= mb_strtoupper($apep);

        return $nombre_user;
    }

    // VISTA CONFIGURACIÓN DE USUARIO
    public function config(User $user)
    {
        return view('users.config', compact('user'));
    }

    // NUEVA CONTRASEÑA POR USUARIOS
    public function cuenta_update(Request $request, User $user)
    {
        if ($request->oldPassword) {
            if (Hash::check($request->oldPassword, $user->password)) {
                if ($request->newPassword == $request->password_confirm) {
                    $user->password = Hash::make($request->newPassword);
                    $user->save();
                    return redirect()->route('users.config', $user->id)->with('bien', 'Contraseña actualizada con éxito');
                } else {
                    return redirect()->route('users.config', $user->id)->with('error', 'Error al confirmar la nueva contraseña');
                }
            } else {
                return redirect()->route('users.config', $user->id)->with('error', 'La contraseña (Antigua contraseña) no coincide con nuestros registros');
            }
        }
    }

    // NUEVA FOTO POR USUARIOS
    public function cuenta_update_foto(Request $request, User $user)
    {
        if ($request->ajax()) {
            if ($request->hasFile('foto')) {
                $archivo_img = $request->file('foto');
                $extension = '.' . $archivo_img->getClientOriginalExtension();
                $codigo = $user->name;
                $path = public_path() . '/imgs/users/' . $user->foto;
                if ($user->foto != 'user_default.png') {
                    \File::delete($path);
                }
                // SUBIENDO FOTO AL SERVIDOR
                if ($user->empleado) {
                    $name_foto = $codigo . $user->empleado->nombre . time() . $extension; //determinar el nombre de la imagen y su extesion
                } else {
                    $name_foto = $codigo . time() . $extension; //determinar el nombre de la imagen y su extesion
                }
                $name_foto = str_replace(' ', '_', $name_foto);
                $archivo_img->move(public_path() . '/imgs/users/', $name_foto); //mover el archivo a la carpeta de destino

                $user->foto = $name_foto;
                $user->save();
                session(['bien' => 'Foto actualizado con éxito']);
                return response()->JSON([
                    'msg' => 'actualizado'
                ]);
            }
        }
    }
}
