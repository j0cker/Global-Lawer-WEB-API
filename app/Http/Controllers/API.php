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
use App\Library\DAO\Abogado;
use App\Library\DAO\Permisos_inter;
use App\Library\VO\ResponseJSON;
use Session;
use Validator;
use App\Library\CLASSES\SMS;

class API extends Controller
{

    public function Ingresar(Request $request){
      
        Log::info('[API][ingresar]');

        Log::info("[API][ingresar] Método Recibido: ". $request->getMethod());


        if($request->isMethod('GET')) {
            
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            Validator::make($request->all(), [
                'correo' => 'required',
                'contPass' => 'required'
              ])->validate();
            
            $serviceT = $request->input('serviceT');
            $correo = $request->input('correo');
            $contPass = $request->input('contPass');
        
            Log::info("[APIAdmin][ingresar] Tipo de Servicio: ". $serviceT);
            Log::info("[APIAdmin][ingresar] correo: ". $correo);
            Log::info("[APIAdmin][ingresar] contPass: ". $contPass);

            if( $serviceT == 0 ){
                Log::info("[APIAdmin][ingresar] Tipo de Servicio: Busco");

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

            } else if ( $serviceT == 1 ){
                Log::info("[APIAdmin][ingresar] Tipo de Servicio: Ofrezo");

                $usuario = Abogado::lookForByEmailAndPass($correo, $contPass)->get();
                Log::info($usuario);

                if(count($usuario)>0){

                    $permisos_inter_object = Permisos_inter::lookForByIdAbogado($usuario->first()->id_abogado)->get();
                    $permisos_inter = array();
                    foreach($permisos_inter_object as $permiso){
                        $permisos_inter[] = $permiso["id_permisos"];
                    }
            
                    $jwt_token = null;
            
                    $factory = JWTFactory::customClaims([
                        'sub'   => $usuario->first()->id_abogado, //id a conciliar del usuario
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

            }

    
            return "";
            
        } else {
            abort(404);
        }
    }

    public function SMS(Request $request){

        Log::info('[APIUsuarios][SMS]');

        Log::info("[APIUsuarios][SMS] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')){

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            $celular = $request->input('celular');
            Log::info('[APIUsuarios][VerificarSMS] Celular: ' . $celular);

            $sms = new SMS();
            $status = $sms->verifyNumber('+52'. $celular);
            Log::info('[APIUsuarios][SMS] Mensaje enviado');

            $obj = Array();
            $obj[0] = new \stdClass();
            $obj[0]->status = $status; //return true in the other one return 1

            Log::info('[APIUserNormal][VerificarSMS] Status de Retorno: ' . $status);

            if($status === 'pending'){
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.SendSMS'), 0);
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            

            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsSendSMS'), 0);
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            }



        } else {
            abort(404);
        }
    }

    public function SMS2(Request $request){

        Log::info('[APIUsuarios][SMS2]');

        Log::info("[APIUsuarios][SMS2] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')){

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            $celular = $request->input('celular');
            Log::info('[APIUsuarios][SMS2] Celular: ' . $celular);

            $sms = new SMS();
            $status = $sms->enviarMensaje('Haz recibidio una solicitud de servicio de abogado','+52'. $celular);
            Log::info('[APIUsuarios][SMS2] Mensaje enviado');

            $obj = Array();
            $obj[0] = new \stdClass();
            $obj[0]->status = $status; //return true in the other one return 1

            Log::info('[APIUserNormal][SMS2] Retorno: ' . $status);

            if($status === 'queued'){
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.SendSMS2'), 0);
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            

            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsSendSMS2'), 0);
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            }



        } else {
            abort(404);
        }
    }

    public function SMSConfirm(Request $request){

        Log::info('[APIUsuarios][SMSConfirm]');

        Log::info("[APIUsuarios][SMSConfirm] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')){

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            $celular = $request->input('celular');
            Log::info('[APIUsuarios][SMSConfirm] Celular: ' . $celular);

            $sms = new SMS();
            $status = $sms->enviarMensaje('El numero de contacto de tu abogado es: ','+52'. $celular);
            Log::info('[APIUsuarios][SMSConfirm] Mensaje enviado');

            $obj = Array();
            $obj[0] = new \stdClass();
            $obj[0]->status = $status; //return true in the other one return 1

            Log::info('[APIUserNormal][SMSConfirm] Retorno: ' . $status);

            if($status === 'queued'){
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.SendSMS2'), 0);
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            

            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsSendSMS2'), 0);
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            }



        } else {
            abort(404);
        }
    }

    public function VerificarSMS(Request $request){

        Log::info('[APIUsuarios][VerificarSMS]');

        Log::info("[APIUsuarios][VerificarSMS] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')){

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            $code = $request->input('code');
            $celular = $request->input('celular');
            Log::info('[APIUsuarios][VerificarSMS] Código: ' . $code);
            Log::info('[APIUsuarios][VerificarSMS] Celular: ' . $celular);

            $sms = new SMS();
            $status = $sms->verifyCode($code, '+52'.$celular);
            Log::info('[APIUsuarios][VerificarSMS] Verificacion de Código');

            
            $obj = Array();
            $obj[0] = new \stdClass();
            $obj[0]->status = $status; //return true in the other one return 1

            Log::info('[APIUserNormal][VerificarSMS] Status de Retorno: ' . $status);

            if($status === 'approved'){
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.VerifiedCode'), 0);
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            

            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsVerifiedCode'), 0);
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            }

            // return $response;

        } else {
            abort(404);
        }
    }

}