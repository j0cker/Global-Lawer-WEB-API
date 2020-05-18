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
use App\Library\DAO\Documentos;
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
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');

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
            Log::info("[APILawyer][registar] Mes de Termino: ". $mesTermino);
            Log::info("[APILawyer][registar] Año de Termino: ". $anoTermino);

            $usuario = Abogado::createUser($correo, $password, $cedula, $nombre, $apellido, $disponibilidad, $celular, $idiomas, $diasLaborales, $hEntrada, $hSalida, $address, $long, $lat, $escuela, $carrera, $anoTermino);
            Log::info($usuario);

            if($usuario[0]->save == 1) {

                Log::info('[APILawyer][registar] Se registro el abogado en todas las tablas, creando permisos');

                $permisos_inter_object = Permisos_inter::createPermisoInterAbogado($usuario[0]->id);

                if ($permisos_inter_object[0]->save == 1) {

                    $permisos_inter_object = Permisos_inter::lookForByIdAbogado($usuario[0]->id)->get();
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

    public function GetProfile(Request $request) {
     
        Log::info('[APILawyer][GetProfile]');

        Log::info("[APILawyer][GetProfile] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {



            header('Access-Control-Allow-Origin: *');
            //header('Access-Control-Allow-Methods: *');
            //header('Access-Control-Allow-Headers: *');

            $token = $request->header('Authorization');

            Log::info('[APILawyer][GetProfile] XSRF: ' . print_r($token, true));
            
            

            $request->merge(['token' => isset($token)? $token : '']);

                    
            $validator = Validator::make($request->all(), [ 
                'token' => 'required',
                'id_user' => 'required'
                
            ]);

            if($validator->fails()){

                Log::info('[APILawyer][GetProfile] fails');
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'), 'Faltan campos', 0);
                $responseJSON->data = [];
                return json_encode($responseJSON);
                
            }
            
            $token = $request->input('token');
            $id_user = $request->input('id_user');

            Log::info("[APILawyer][GetProfile] Token: ". $token);
            Log::info("[APILawyer][GetProfile] ID User: ". $id_user);

            try {

                // attempt to verify the credentials and create a token for the user
                $token = JWTAuth::getToken();
                $token_decrypt = JWTAuth::getPayload($token)->toArray();

                Log::info("Token permisos: " . print_r($token_decrypt["permisos"],true));

                if(in_array(1, $token_decrypt["permisos"])){
                    // $id_usuarios = $token_decrypt["usr"]->id_usuarios;   
                    $usuario = Abogado::getProfile($id_user);
                
                    Log::info($usuario);
            
                    if(count($usuario)>0){
                    
                    $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDsuccess'), count($usuario));
                    $responseJSON->data = $usuario;
                    return json_encode($responseJSON);
            
                    } else {
            
                    $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBD'), count($usuario));
                    $responseJSON->data = [];
                    return json_encode($responseJSON);
            
                    }

                } else{
                    $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBD'), 0);
                    $responseJSON->data = [];
                    return json_encode($responseJSON);
                }
        
              } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        
                //token_expired
            
                Log::info('[APILawyer][GetIdiomaObtener] Token error: token_expired');
        
                return redirect('/');
          
              } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        
                //token_invalid
            
                Log::info('[APILawyer][GetIdiomaObtener] Token error: token_invalid');
        
                return redirect('/');
          
              } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        
                //token_absent
            
                Log::info('[APILawyer][GetIdiomaObtener] Token error: token_absent');
        
                return redirect('/');
          
              }

        }
    }

    public function GetLaw(Request $request) {
     
        Log::info('[APILawyer][GetLaw]');

        Log::info("[APILawyer][GetLaw] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            /*
            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            */

            // $token = $request->input('token');
            $cedula = $request->input('cedula');

            // Log::info("[APILawyer][GetProfile] Token: ". $token);
            Log::info("[APILawyer][GetLaw] Cedula Law: ". $cedula);

            // $id_usuarios = $token_decrypt["usr"]->id_usuarios;   
            $usuario = Abogado::getProfile2($cedula);
        
            Log::info($usuario);
    
            if(count($usuario)>0){
            
            $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDsuccess'), count($usuario));
            $responseJSON->data = $usuario;
            return json_encode($responseJSON);
    
            } else {
    
            $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBD'), count($usuario));
            $responseJSON->data = [];
            return json_encode($responseJSON);
    
            }

        }
    }

    public function UploadDoc(Request $request) {

        Log::info('[APILawyer][UploadDoc]');

        Log::info("[APILawyer][UploadDoc] Método Recibido: ". $request->getMethod());

        if($request->isMethod('POST')) {

            header('Access-Control-Allow-Origin: *');
            //header('Access-Control-Allow-Methods: *');
            //header('Access-Control-Allow-Headers: *');

            //$request->merge(['token' => isset($_COOKIE["token"])? $_COOKIE["token"] : (empty($request->header('Authorization'))? '' : $request->header('Authorization'))]);

                    
            $validator = Validator::make($request->all(), [ 
                //'token' => 'required',
                'id_usuarios' => 'required',
                'id_tipo_usuarios' => 'required',
                'id_imagen' => 'required',
                'img' => 'required'
                
            ]);

            if($validator->fails()){

                Log::info('[APILawyer][GetProfile] fails');
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'), 'Faltan campos', 0);
                $responseJSON->data = [];
                return json_encode($responseJSON);
                
            }


            $id_usuarios = $request->input('id_usuarios');
            $id_tipo_usuarios = $request->input('id_tipo_usuarios');
            $id_imagen = $request->input('id_imagen');
            $img = $request->input('img');

            Log::info("[APILawyer][UploadDoc] ID Usuarios: ". $id_usuarios);
            Log::info("[APILawyer][UploadDoc] ID Tipo Usuarios: ". $id_tipo_usuarios);
            Log::info("[APILawyer][UploadDoc] Id Imagen: ". $id_imagen);
            Log::info("[APILawyer][UploadDoc] IMG: ". strlen($img));

            $usuario = Documentos::uploadImg( $id_usuarios, $id_tipo_usuarios, $id_imagen, $img );
            Log::info($usuario);

            if($usuario[0]->save == 1) {

                Log::info('[APILawyer][UploadDoc] Se registro el documento en todas las tablas, creando permisos');

                $permisos_inter_object = Permisos_inter::createPermisoInterAbogado($usuario[0]->id);

                if ($permisos_inter_object[0]->save == 1) {

                    $permisos_inter_object = Permisos_inter::lookForByIdAbogado($usuario[0]->id)->get();
                    $permisos_inter = array();
                    foreach($permisos_inter_object as $permiso){
                        $permisos_inter[] = $permiso["id_permisos"];
                    }
            
                    Log::info("[API][UploadDoc] Permisos: ");
                    Log::info($permisos_inter);
                    
                    $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), count($usuario));
                    $responseJSON->data = $usuario;
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

}