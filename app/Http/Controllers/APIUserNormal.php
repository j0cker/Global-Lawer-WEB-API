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
use Ixudra\Curl\Facades\Curl;

class APIUserNormal extends Controller
{

    public function VerifyCedula(Request $request) {

        Log::info('[APIUserNormal][Prueba]');

        Log::info("[APIUserNormal][Prueba] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            $cedula = $request->input('cedula');
            Log::info('[APIUserNormal][Prueba] Cédula: ' . $cedula);


            $response = json_decode(Curl::to('http://search.sep.gob.mx/solr/cedulasCore/select?fl=%2A%2Cscore&q='.$cedula.'&start=0&rows=100&facet=true&indent=on&wt=json')->withOption('USERAGENT', $request->header('User-Agent'))->get());


            $obj = Array();
            $obj[0] = new \stdClass();
            $obj[0]->num_found = $response->response->numFound; //return true in the other one return 1
            $obj[0]->docs = $response->response->docs;

            //Log::info('[APIUserNormal][Prueba] Retorno: ' . print_r($obj));

            if($response->response->numFound!=0){
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), count($obj[0]->num_found));
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            

            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBDFail'), count($obj[0]->num_found));
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            }
            
            //return $obj;


        } else {
            abort(404);
        }

        
    }

    public function Registrar(Request $request){
      
        Log::info('[APIUserNormal][registrar]');

        Log::info("[APIUserNormal][registar] Método Recibido: ". $request->getMethod());


        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');
            
            /*
            Validator::make($request->all(), [
                'nombre' => 'required',
                'apellido' => 'required',
                'correo' => 'required',
                'telefono' => 'required',
                'cel' => 'required',
              ])->validate();
            */    
              //Log::info('[APIUserNormal][registrar]2');
            
            $nombre = $request->input('nombre');
            $apellido = $request->input('apellido');
            $correo = $request->input('correo');
            $telefono = $request->input('telefono');
            $cel = $request->input('cel');
            $pass = $request->input('pass');
        
            Log::info("[APIUserNormal][registar] nombre: ". $nombre);
            Log::info("[APIUserNormal][registar] apellido: ". $apellido);
            Log::info("[APIUserNormal][registar] correo: ". $correo);
            Log::info("[APIUserNormal][registar] telefono: ". $telefono);
            Log::info("[APIUserNormal][registar] cel: ". $cel);
            Log::info("[APIUserNormal][registar] password: ". $pass);
        
                
            $usuario = Usuarios::createUser($nombre, $apellido, $correo, $telefono, $cel, $pass);
            Log::info($usuario);
    
            if($usuario[0]->save == 1){

                Log::info('[APIUsuarios][registar] Se registro el usuario en todas las tablas, creando permisos');

                $permisos_inter_object = Permisos_inter::createPermisoInter($usuario[0]->id);

                if ($permisos_inter_object[0]->save == 1) {

                    $permisos_inter_object = Permisos_inter::lookForByIdUsuarios($usuario[0]->id)->get();
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