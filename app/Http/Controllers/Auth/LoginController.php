<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\SesionUser;
use App\User;
use DateTime;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'name';
    }
    public function login(Request $request)
    {
        $request->validate([
            "name" => "required",
            "password" => "required"
        ]);

        $name = $request->name;
        $password = $request->password;
        $usuario = User::where('name', '=', $request->name)->get()->first();
        if ($usuario) {
            if ($usuario->status == 0 || $usuario->estado == 'INACTIVO') {
                return redirect()->back()->with('error_c', 'cerrado');
            }
        }
        $ingreso_libre = false;
        if (!Auth::check()) {
            $ingreso_libre = true;
        }
        $res = Auth::attempt(['name' => $name, 'password' => $password]);
        if ($res) {
            // registrar sesión
            $agenteDeUsuario = $_SERVER["HTTP_USER_AGENT"];
            $familiaNavegador = $this->getBrowserName($agenteDeUsuario); // Chrome, Firefox, Safari, Edge
            $navegador =  $this->getBrowserName($agenteDeUsuario);
            $dispositivo = $this->getDeviceName($agenteDeUsuario);
            $familiaSistema = $this->getOperatingSystem($agenteDeUsuario);
            $sistema = $this->getOperatingSystem($agenteDeUsuario);
            $completo = $navegador . ' / ' . $sistema;

            // comprobar si existe una ultima sesion del usuario
            $user = Auth::user();
            if ($user->sesion_user) {
                // validar estado
                $info = [
                    "navegador" => $navegador,
                    "dispositivo" => $dispositivo,
                    "sistema" => $sistema,
                    "completo" => $completo,
                ];

                $actual = date("Y-m-d H:i:s");
                $fecha_sesion = date("Y-m-d H:i:s", strtotime($user->sesion_user->updated_at));
                $duracion_ultima_sesion = $this->diferenciaEnMinutos($actual, $fecha_sesion);
                // Log::debug($actual);
                // Log::debug($fecha_sesion);
                // Log::debug($duracion_ultima_sesion);
                if ($ingreso_libre && $user->sesion_user->navegador == $navegador && $user->sesion_user->dispositivo == $dispositivo && $user->sesion_user->sistema == $sistema && $user->sesion_user->detalle == $completo) {
                    Auth::logout();
                    Auth::attempt(['name' => $name, 'password' => $password]);
                    // actualizar los datos para la nueva sesión
                    $user->sesion_user->update([
                        "navegador" => $navegador,
                        "dispositivo" => $dispositivo,
                        "sistema" => $sistema,
                        "detalle" => $completo,
                        "estado" => 1,
                    ]);
                } else {
                    if ($user->sesion_user->estado == 1 && $duracion_ultima_sesion < 5) {
                        // eliminar la sesion que se creo y devolver error
                        Auth::logout();
                        return redirect()->back()->with('error_session', 'Ya tienes una sesión iniciada en otro lugar. Asegurate de cerrar sesión primero en ese dispositivo primero.');
                    } else {
                        if ($duracion_ultima_sesion >= 5) {
                            Auth::logout();
                        }
                        Auth::attempt(['name' => $name, 'password' => $password]);
                        // actualizar los datos para la nueva sesión
                        $user->sesion_user->update([
                            "navegador" => $navegador,
                            "dispositivo" => $dispositivo,
                            "sistema" => $sistema,
                            "detalle" => $completo,
                            "estado" => 1,
                        ]);
                    }
                }
            } else {
                $user->sesion_user()->create([
                    "navegador" => $navegador,
                    "dispositivo" => $dispositivo,
                    "sistema" => $sistema,
                    "detalle" => $completo,
                    "estado" => 1,
                ]);
            }
            return $this->sendLoginResponse($request);
        }
        return $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->sesion_user->update([
            "estado" => 0,
        ]);

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('login');
    }

    private function diferenciaEnMinutos($fecha1, $fecha2)
    {
        // Convertir las cadenas de fecha a objetos DateTime
        $fechaObjeto1 = new DateTime($fecha1);
        $fechaObjeto2 = new DateTime($fecha2);

        // Calcular la diferencia en minutos
        $diferencia = $fechaObjeto1->diff($fechaObjeto2);

        // Obtener el total de minutos en la diferencia
        $minutos = ($diferencia->days * 24 * 60) +
            ($diferencia->h * 60) +
            $diferencia->i;

        return $minutos;
    }

    private function mismoNavegador($user, $info)
    {
        // actualizar los datos para la nueva sesión
        $existe = SesionUser::where("navegador", $info["navegador"])
            ->where("user_id", $user->id)
            ->where("dispositivo", $info["dispositivo"])
            ->where("sistema", $info["sistema"])
            ->where("detalle", $info["completo"])
            ->where("estado", 1)
            ->get()->first();
        return $existe;
    }

    private function getBrowserName($userAgent)
    {
        $browsers = [
            'Opera' => 'Opera',
            'Edge' => 'Edge',
            'Chrome' => 'Chrome',
            'Safari' => 'Safari',
            'Firefox' => 'Firefox',
            'MSIE' => 'Internet Explorer',
        ];

        foreach ($browsers as $key => $value) {
            if (strpos($userAgent, $key) !== false) {
                return $value;
            }
        }

        return 'Unknown';
    }

    private function getOperatingSystem($userAgent)
    {
        $operatingSystems = [
            'Windows' => 'Windows',
            'Macintosh' => 'Macintosh',
            'Linux' => 'Linux',
            'iOS' => 'iOS',
            'Android' => 'Android',
        ];

        foreach ($operatingSystems as $key => $value) {
            if (strpos($userAgent, $key) !== false) {
                return $value;
            }
        }

        return 'Unknown';
    }

    private function getDeviceName($userAgent)
    {
        // Puedes agregar más dispositivos aquí si lo deseas
        $devices = [
            'iPhone' => 'iPhone',
            'iPad' => 'iPad',
            'Android' => 'Android',
            'Windows Phone' => 'Windows Phone',
        ];

        foreach ($devices as $key => $value) {
            if (strpos($userAgent, $key) !== false) {
                return $value;
            }
        }

        return 'Unknown';
    }

    public function expired()
    {
        if (Auth::check()) {
            // La sesión del usuario ha expirado
            // Realiza cualquier acción necesaria, como cerrar sesión o guardar datos
            Auth::logout();
        }

        // Redirige al usuario a la página inicial o a otra página
        return redirect()->route("login");
    }
}
