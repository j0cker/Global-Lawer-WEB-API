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
use App\Library\DAO\Usuarios;
use App\Library\DAO\Permisos_inter;
use App\Library\VO\ResponseJSON;
use Session;
use Validator;

class APIUserNormal extends Controller
{

    public function Registrar(Request $request){
      
        Log::info('[APIUserNormal][registrar]');

        Log::info("[APIUserNormal][registar] Método Recibido: ". $request->getMethod());


        if($request->isMethod('POST')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            Validator::make($request->all(), [
                'nombre' => 'required',
                'apellido' => 'required',
                'correo' => 'required',
                'telefono' => 'required',
                'cel' => 'required',
              ])->validate();
            
            $nombre = $request->input('nombre');
            $apellido = $request->input('apellido');
            $correo = $request->input('correo');
            $telefono = $request->input('telefono');
            $cel = $request->input('cel');
        
            Log::info("[APIUserNormal][registar] nombre: ". $nombre);
            Log::info("[APIUserNormal][registar] apellido: ". $apellido);
            Log::info("[APIUserNormal][registar] correo: ". $correo);
            Log::info("[APIUserNormal][registar] telefono: ". $telefono);
            Log::info("[APIUserNormal][registar] cel: ". $cel);
        
                
            $usuario = Usuarios::createUser($nombre, $apellido, $correo, $telefono, $cel);
            Log::info($usuario);
    
            if($usuario == 1){

                $permisos_inter_object = Permisos_inter::lookForByIdUsuarios($usuario->first()->id_usuarios)->get();
                $permisos_inter = array();
                foreach($permisos_inter_object as $permiso){
                    $permisos_inter[] = $permiso["id_permisos"];
                }
        
                $jwt_token = null;
        
                $factory = JWTFactory::customClaims([
                    'sub'   => $usuario->first()->id_usuarios, //id a conciliar del usuario
                    'iss'   => config('app.name'),
                    'iat'   => Carbon::now()->timestamp,
                    'exp'   => Carbon::tomorrow()->timestamp,
                    'nbf'   => Carbon::now()->timestamp,
                    'jti'   => uniqid(),
                    'usr'   => $usuario->first(),
                    'permisos' => $permisos_inter,
                ]);
                
                $payload = $factory->make();
                
                $jwt_token = JWTAuth::encode($payload);
                Log::info("[API][ingresar] new token: ". $jwt_token->get());
                Log::info("[API][ingresar] Permisos: ");
                Log::info($permisos_inter);
                
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), count($usuario));
                $responseJSON->data = $usuario;
                $responseJSON->token = $token->get();
                return json_encode($responseJSON);
        
            
    
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