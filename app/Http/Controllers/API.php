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

class API extends Controller
{

    public function Ingresar(Request $request){
      
        Log::info('[API][ingresar]');

        Log::info("[API][ingresar] Método Recibido: ". $request->getMethod());


        if($request->isMethod('GET')) {
            
            $correo = $request->input('correo');
            $contPass = $request->input('contPass');
        
            Log::info("[APIAdmin][ingresar] correo: ". $correo);
            Log::info("[APIAdmin][ingresar] contPass: ". $contPass);

                
            $usuario = Usuarios::lookForByEmailAndPass($correo, $contPass)->get();
            Log::info($usuario);
    
            if(count($usuario)>0){

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
                $responseJSON->token = $jwt_token->get();
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