<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Lang;
use App;
use App\Library\CLASSES\QueueMails;
use Config;
use Auth;
use carbon\Carbon;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use JWTFactory;
use Tymon\JWTAuth\PayloadFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Library\DAO\Abogado;
use App\Library\DAO\Servicios;
use App\Library\DAO\Documentos;
use App\Library\DAO\Empresa;
use App\Library\DAO\Permisos_inter;
use App\Library\DAO\Usuarios;
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

                $data['name'] = $nombre . ' ' . $apellido;
                //Send to queue email list of administrator mail
                $data['user_id'] = $usuario[0]->id;
                $data['tipo'] = "Abogado";
                $data['email'] = $correo;
                $data['password'] = $password;
                $data['verification_code'] = 1234;
                //$data['body'] = "".Lang::get('messages.emailSubscribeBody')."".$email."";
                //$data['subject'] = Lang::get('messages.emailSubscribeSubject');
                //$data['priority'] = 1;
                $mail = new QueueMails($data);
                $mail->welcome();

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
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');

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

    public function DespachosCards(Request $request){
      
        Log::info('[APILawyer][DespachosCards]');

        Log::info("[APILawyer][DespachosCards] Método Recibido: ". $request->getMethod());


        if($request->isMethod('GET')) {
            
            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');

           /* Validator::make($request->all(), [
                'correo' => 'required'
              ])->validate();
            */
            
            // $correo = $request->input('correo');        
            // Log::info("[APIAdmin][Prueba] correo: ". $correo);
                
            $usuario = Empresa::despachosCards();
            Log::info($usuario);
    
            if(count($usuario)>0){
        
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), count($usuario));
                $responseJSON->data = $usuario;
                // $responseJSON->token = $jwt_token->get();
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

            $tipo = $request->input('tipo');
            $cedula = $request->input('cedula');

            Log::info("[APILawyer][GetLaw] Tipo: ". $tipo);
            Log::info("[APILawyer][GetLaw] Cedula Law: ". $cedula);

            // $id_usuarios = $token_decrypt["usr"]->id_usuarios;   
            $usuario = Abogado::getProfile2($tipo, $cedula);
        
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

    public function GetChatService(Request $request) {
     
        Log::info('[APILawyer][getChatService]');

        Log::info("[APILawyer][getChatService] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');

            /*
            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            */

            $id_abogado = $request->input('id_abogado');

            Log::info("[APILawyer][getChatService] ID Abogado: ". $id_abogado);

            $usuario = Servicios::getChatServicio('1', $id_abogado);
        
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

    public function GetService(Request $request) {
     
        Log::info('[APILawyer][GetService]');

        Log::info("[APILawyer][GetService] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');

            /*
            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            */

            $id_abogado = $request->input('id_abogado');

            Log::info("[APILawyer][GetService] ID Abogado: ". $id_abogado);

            $usuario = Servicios::getServicio('1', $id_abogado);
        
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

    public function UpdateLaw(Request $request) {
     
        Log::info('[APILawyer][UpdateLaw]');

        Log::info("[APILawyer][UpdateLaw] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');

            /*
            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            */

            $id_abogado = $request->input('id_abogado');
            $acercaDe = $request->input('acercaDe');
            $nombre = $request->input('nombre');
            $apellido = $request->input('apellido');

            Log::info("[APILawyer][UpdateLaw] ID Abogado: ". $id_abogado);
            Log::info("[APILawyer][UpdateLaw] Acerca de: ". $acercaDe);
            Log::info("[APILawyer][UpdateLaw] Nombre: ". $nombre);
            Log::info("[APILawyer][UpdateLaw] Apellido: ". $apellido);

            $usuario = Abogado::updateLaw($id_abogado, $acercaDe, $nombre, $apellido);
        
            Log::info($usuario);
    
            if($usuario == 1){
            
                Log::info('[APIUsuarios][UpdateLaw] Se actualizo los datos de usuario en la tabla Usuarios');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBDFail'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";

        }
    }

    public function UpdateLawCedula(Request $request) {
     
        Log::info('[APILawyer][UpdateLawCedula]');

        Log::info("[APILawyer][UpdateLawCedula] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');

            /*
            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            */

            $id_abogado = $request->input('id_abogado');
            $cedula = $request->input('cedula');

            Log::info("[APILawyer][UpdateLawCedula] ID Abogado: ". $id_abogado);
            Log::info("[APILawyer][UpdateLawCedula] Cédula: ". $cedula);

            $usuario = Abogado::updateLawCedula($id_abogado, $cedula);
        
            Log::info($usuario);
    
            if($usuario == 1){
            
                Log::info('[APIUsuarios][UpdateLaw] Se actualizo los datos de usuario en la tabla Usuarios');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBDFail'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";

        }
    }

    public function UpdatePayment(Request $request) {
     
        Log::info('[APILawyer][UpdatePayment]');

        Log::info("[APILawyer][UpdatePayment] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');

            /*
            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            */

            $token = $request->input('token');
            $id_abogado = $request->input('id_abogado');
            $servicePayment = $request->input('servicePayment');
            $amount = $request->input('amount');

            Log::info("[APILawyer][UpdatePayment] Token: ". $token);
            Log::info("[APILawyer][UpdatePayment] ID Abogado: ". $id_abogado);
            Log::info("[APILawyer][UpdatePayment] ServicePayment: ". $servicePayment);
            Log::info("[APILawyer][UpdatePayment] Amount: ". $amount);

            $usuario = Abogado::updatePayment($id_abogado, $servicePayment, $amount);
        
            Log::info($usuario);
    
            if($usuario == 1){
            
                Log::info('[APIUsuarios][UpdateLaw] Se actualizo los datos de usuario en la tabla Usuarios');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBDFail'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";

        }
    }

    public function GetServiciosUsuarios(Request $request) {
     
        Log::info('[APILawyer][GetServiciosUsuarios]');

        Log::info("[APILawyer][GetServiciosUsuarios] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');

            /*
            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            */

            $id_servicios = $request->input('id_servicios');

            Log::info("[APILawyer][GetServiciosUsuarios] ID Usuarios: ". $id_servicios);

            $usuario = Servicios::getServiciosUsuarios($id_servicios);
        
            Log::info($usuario);
    
            if(count($usuario)>0){
            
                Log::info('[APIUsuarios][UpdateLaw] Se actualizo los datos de usuario en la tabla Usuarios');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBDFail'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";

        }
    }

    public function UpdateLawAddress(Request $request) {
     
        Log::info('[APILawyer][UpdateLawAddress]');

        Log::info("[APILawyer][UpdateLawAddress] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');

            $id_abogado = $request->input('id_abogado');
            $address = $request->input('address');

            Log::info("[APILawyer][UpdateLawAddress] ID Abogado: ". $id_abogado);
            Log::info("[APILawyer][UpdateLawAddress] Direccion: ". $address);

            $usuario = Abogado::updateLawAddress($id_abogado, $address);
        
            Log::info($usuario);
    
            if($usuario == 1){
            
                Log::info('[APIUsuarios][UpdateLaw] Se actualizo los datos de usuario en la tabla Usuarios');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBDFail'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";

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
                'id_empresa' => 'required',
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
            $id_empresa = $request->input('id_empresa');
            $id_imagen = $request->input('id_imagen');
            $img = $request->input('img');

            Log::info("[APILawyer][UploadDoc] ID Usuarios: ". $id_usuarios);
            Log::info("[APILawyer][UploadDoc] ID Tipo Usuarios: ". $id_tipo_usuarios);
            Log::info("[APILawyer][UploadDoc] Id Empresa: ". $id_empresa);
            Log::info("[APILawyer][UploadDoc] Id Imagen: ". $id_imagen);
            Log::info("[APILawyer][UploadDoc] IMG: ". strlen($img));

            $usuario = Documentos::uploadImg( $id_usuarios, $id_tipo_usuarios, $id_empresa, $id_imagen, $img );
            Log::info($usuario);

            if($usuario[0]->save == 1) {

                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), count($usuario));
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);

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

    public function AddServicios(Request $request) {
     
        Log::info('[APILawyer][AddServicios]');

        Log::info("[APILawyer][AddServicios] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');

            /*
            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            */

            $token = $request->input('token');
            $id_usuario = $request->input('id_usuario');
            $servicio = $request->input('servicio');

            Log::info("[APILawyer][UpdateLaw] Token: ". $id_usuario);
            Log::info("[APILawyer][UpdateLaw] ID Usuario: ". $id_usuario);
            Log::info("[APILawyer][UpdateLaw] Servicios: ". $servicio);

            $usuario = Abogado::addServicios($id_usuario, $servicio);
        
            Log::info($usuario);
    
            if($usuario == 1){
            
                Log::info('[APIUsuarios][UpdateLaw] Se actualizo los datos de usuario en la tabla Usuarios');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBDFail'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";

        }
    }

    public function ReporteAbogado(Request $request) {
     
        Log::info('[APILawyer][ReporteAbogado]');

        Log::info("[APILawyer][ReporteAbogado] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            // header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');

            /*
            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            */

            $usuario = $request->input('usuario');
            $abogado = $request->input('abogado');
            $reporte = $request->input('reporte');

            Log::info("[APILawyer][UpdateLaw] Usuario: ". $usuario);
            Log::info("[APILawyer][UpdateLaw] Abogado: ". $abogado);
            Log::info("[APILawyer][UpdateLaw] Reporte: ". $reporte);

            $data['name'] = 'Administrador';
            //Send to queue email list of administrator mail
            $data['user_id'] = '1';
            $data['to'] = 'info@boogapp.mx';
            $data['priority'] = '3';
            $data['tipo'] = 'Reporte';
            $data['subject'] = 'Reporte de abogado';
            $data['body'] = 'El usuario ' . $usuario . ' ha reportado al abogado ' . $abogado . '<br> Reporte: <br> "' . $reporte .'"' ;
            // $data['subject'] = Lang::get('messages.emailSubscribeSubject');
            //$data['priority'] = 1;
            $mail = new QueueMails($data);
            $mail->customMailUnique();
    
            return "";

        }
    }

}