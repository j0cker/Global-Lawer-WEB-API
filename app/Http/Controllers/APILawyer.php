<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Lang;
use App;
use Config;
use Auth;
use carbon\Carbon;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use JWTFactory;
use Tymon\JWTAuth\PayloadFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Library\DAO\Abogado;
use App\Library\DAO\Permisos_inter;
use App\Library\VO\ResponseJSON;
use Session;
use Validator;
use Ixudra\Curl\Facades\Curl;

class APILawyer extends Controller
{

    public function Registrar(Request $request) {

        Log::info('[APILawyer][registrar]');

        Log::info("[APILawyer][registar] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            $correo = $request->input('correo');
            $password = $request->input('password');
            $cedula = $request->input('cedula');
            $nombre = $request->input('nombre');
            $apellido = $request->input('apellido');
            $disponibilidad = $request->input('disponibilidad');
            $celular = $request->input('celular');
            $idiomas = $request->input('idiomas');
            $diasLaborales = $request->input('diasLaborales');
            $hEntrada = $request->input('hEntrada');
            $hSalida = $request->input('hSalida');
            $address = $request->input('address');
            $long = $request->input('long');
            $lat = $request->input('lat');
            $escuela = $request->input('escuela');
            $carrera = $request->input('carrera');
            $mesInicio = $request->input('mesInicio');
            $anoInicio = $request->input('anoInicio');
            $mesTermino = $request->input('mesTermino');
            $anoTermino = $request->input('anoTermino');

            Log::info("[APILawyer][registar] Correo: ". $correo);
            Log::info("[APILawyer][registar] Password: ". $password);
            Log::info("[APILawyer][registar] Cedula: ". $cedula);
            Log::info("[APILawyer][registar] Nombre: ". $nombre);
            Log::info("[APILawyer][registar] Apellido: ". $apellido);
            Log::info("[APILawyer][registar] Disponibilidad: ". $disponibilidad);
            Log::info("[APILawyer][registar] Celular: ". $celular);
            Log::info("[APILawyer][registar] Idiomas: ". $idiomas);
            Log::info("[APILawyer][registar] Dias Laborales: ". $diasLaborales);
            Log::info("[APILawyer][registar] Hora de Entrada: ". $hEntrada);
            Log::info("[APILawyer][registar] Hora de Salida: ". $hSalida);
            Log::info("[APILawyer][registar] Direccion: ". $address);
            Log::info("[APILawyer][registar] Longitud: ". $long);
            Log::info("[APILawyer][registar] Latitud: ". $lat);
            Log::info("[APILawyer][registar] Escuela: ". $escuela);
            Log::info("[APILawyer][registar] Carrera: ". $carrera);
            Log::info("[APILawyer][registar] Mes de Inicio: ". $mesInicio);
            Log::info("[APILawyer][registar] Año de Inicio: ". $anoInicio);
            Log::info("[APILawyer][registar] Mes de Termino: ". $mesTermino);
            Log::info("[APILawyer][registar] Año de Termino: ". $anoTermino);

            $usuario = Abogado::createUser($correo, $password, $cedula, $nombre, $apellido, $disponibilidad, $celular, $idiomas, $diasLaborales, $hEntrada, $hSalida, $address, $long, $lat, $escuela, $carrera, $mesInicio, $anoInicio, $mesTermino, $anoTermino);
            Log::info($usuario);

            if($usuario[0]->save == 1) {

                Log::info('[APILawyer][registar] Se registro el abogado en todas las tablas, creando permisos');

                $permisos_inter_object = Permisos_inter::createPermisoInter($usuario[0]->id);

                if ($permisos_inter_object[0]->save == 1) {

                    $permisos_inter_object = Permisos_inter::lookForByIdLawyer($usuario[0]->id)->get();
                    $permisos_inter = array();
                    foreach($permisos_inter_object as $permiso){
                        $permisos_inter[] = $permiso["id_permisos"];
                    }
            
                    $jwt_token = null;
            
                    $factory = JWTFactory::customClaims([
                        'sub'   => $usuario[0]->id, //id a conciliar del usuario
                        'iss'   => config('app.name'),
                        'iat'   => Carbon::now()->timestamp,
                        'exp'   => Carbon::tomorrow()->timestamp,
                        'nbf'   => Carbon::now()->timestamp,
                        'jti'   => uniqid(),
                        'usr'   => $usuario[0],
                        'permisos' => $permisos_inter,
                    ]);
                    
                    $payload = $factory->make();
                    
                    $jwt_token = JWTAuth::encode($payload);
                    Log::info("[API][ingresar] new token: ". $jwt_token->get());
                    Log::info("[API][ingresar] Permisos: ");
                    Log::info($permisos_inter);
                    
                    $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), count($usuario));
                    $responseJSON->data = $usuario;
                    $responseJSON->token = $jwt_token->get();
                    return json_encode($responseJSON);

                }

            }

            else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBDFail'), count($usuario));
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";

        } else {
            abort(404);
        }
    }

    public function AbogadosCards(Request $request){
      
        Log::info('[APILawyer][AbogadosCards]');

        Log::info("[APILawyer][AbogadosCards] Método Recibido: ". $request->getMethod());


        if($request->isMethod('GET')) {
            
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

           /* Validator::make($request->all(), [
                'correo' => 'required'
              ])->validate();
            */
            
            // $correo = $request->input('correo');        
            // Log::info("[APIAdmin][Prueba] correo: ". $correo);
                
            $usuario = Abogado::abogadosCards();
            Log::info($usuario);
    
            if(count($usuario)>0){
        
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), count($usuario));
                $responseJSON->data = $usuario;
                // $responseJSON->token = $jwt_token->get();
                return json_encode($responseJSON->data);
        
            
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBDFail'), count($usuario));
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";
            
        } else {
            abort(404);
        }
    }

}