<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Lang;
use App;
use App\Library\CLASSES\PushNotification;
use App\Library\CLASSES\QueueMails;
use Config;
use Auth;
use carbon\Carbon;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use JWTFactory;
use Tymon\JWTAuth\PayloadFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Library\DAO\Usuarios;
use App\Library\DAO\Servicios;
use App\Library\DAO\Abogado;
use App\Library\DAO\Empresa;
use App\Library\DAO\ValoracionUsuario;
use App\Library\DAO\Permisos_inter;
use App\Library\VO\ResponseJSON;
use Session;
use Validator;
use App\Library\CLASSES\SMS;
use App\Library\DAO\Documentos;

class API extends Controller
{

    public function PushNotification(Request $request){

        Log::info('[API][PushNotification]');

        Log::info("[API][PushNotification] Método Recibido: ". $request->getMethod());

        if($request->isMethod('POST')){

            
            header('Access-Control-Allow-Origin: *');

            $validator = Validator::make($request->all(), [ 
                //'token' => 'required',
                'mensaje' => 'required',
                'titulo' => 'required',
                'id_dispositivo' => 'required',
                
            ]);

            if($validator->fails()){

                Log::info('[API][PushNotification] fails');
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'), 'Faltan campos', 0);
                $responseJSON->data = [];
                return json_encode($responseJSON);
                
            }

            $mensaje = $request->input('mensaje');
            $titulo = $request->input('titulo');
            $id_dispositivo = $request->input('id_dispositivo');

            Log::info("[APILawyer][UploadDoc] Mensaje: ". $mensaje);
            Log::info("[APILawyer][UploadDoc] Titulo: ". $titulo);
            Log::info("[APILawyer][UploadDoc] Id Dispositivo: ". $id_dispositivo);

            $pushNotif = new PushNotification();
            $status = $pushNotif->sendMessage($mensaje, $titulo, $id_dispositivo);

            $return["allresponses"] = $status;
            $return = json_encode( $return);
            Log::info('[API][PushNotification]: ' . $status);


        } else {
            abort(404);
        }
    }

    public function UpdateIdDispositivo(Request $request){
  
        Log::info('[API][UpdateIdDispositivo]');

        Log::info("[API][UpdateIdDispositivo] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            /*
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');
            */

            $tipo_usuario = $request->input('tipo_usuario');
            $id_usuario = $request->input('id_usuario');
            $idDispositivo = $request->input('idDispositivo');

            Log::info("[API][UpdateIdDispositivo] Tipo Usuario: ". $tipo_usuario);
            Log::info("[API][UpdateIdDispositivo] ID del Usuario: ". $id_usuario);
            Log::info("[API][UpdateIdDispositivo] ID del Dispositivo: ". $idDispositivo);


            if( $tipo_usuario == '0' ) {
                $usuario = Usuarios::updateIdDispositivo($id_usuario,$idDispositivo);
            } else if( $tipo_usuario == '1' ) {
                $usuario = Abogado::updateIdDispositivo($id_usuario,$idDispositivo);
            }
  
            Log::info($usuario);
            if($usuario == 1){

                Log::info('[API][UpdateIdDispositivo] Se actualizo el ID del dispositivo');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsChangePass'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";
        }
    }

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

    public function EmpresaPost(Request $request){
      
        Log::info('[API][EmpresaPost]');

        Log::info("[API][EmpresaPost] Método Recibido: ". $request->getMethod());


        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');
            
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
            
            $tipo_usuario = $request->input('tipo_usuario');
            $id_responsable = $request->input('id_responsable');
            $nombre = $request->input('nombre');
            $corrreoResponsable = $request->input('corrreoResponsable');
            $telefono = $request->input('telefono');
            $giro = $request->input('giro');
            $tiempoConstitucion = $request->input('tiempoConstitucion');
            $servicio = $request->input('servicio');
            $acercaDe = $request->input('acercaDe');

            Log::info("[API][EmpresaPost] Tipo de Usuario: ". $tipo_usuario);
            Log::info("[API][EmpresaPost] ID Responsable: ". $id_responsable);
            Log::info("[API][EmpresaPost] Nombre: ". $nombre);
            Log::info("[API][EmpresaPost] Correo: ". $corrreoResponsable);
            Log::info("[API][EmpresaPost] Telefono: ". $telefono);
            Log::info("[API][EmpresaPost] Giro: ". $giro);
            Log::info("[API][EmpresaPost] Tiempo de Constitucion de la Empresa: ". $tiempoConstitucion);
            Log::info("[API][EmpresaPost] Servicios: ". $servicio);
            Log::info("[API][EmpresaPost] Acerca de: ". $acercaDe);        
            
            $usuario = Empresa::createEmpresaLaw($tipo_usuario, $id_responsable, $nombre, $corrreoResponsable, $telefono, $giro, $tiempoConstitucion, $servicio, $acercaDe);

            Log::info($usuario);
    
            if($usuario[0]->save == 1){

                $data['name'] = 'Administrador';
                //Send to queue email list of administrator mail
                $data['user_id'] = '1';
                $data['to'] = 'info@boogapp.mx';
                $data['priority'] = '3';
                $data['tipo'] = 'Documentos';
                $data['subject'] = 'Documentos para revisión';
                $data['body'] = 'El Lic. ' . $nombre . ' ha enviado los documentos para su revisión. <br> Favor de revisarlos en el panel de administración y enviarle una respuesta.' ;
                // $data['subject'] = Lang::get('messages.emailSubscribeSubject');
                //$data['priority'] = 1;
                $mail = new QueueMails($data);
                $mail->customMailUnique();


                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), count($usuario));
                $responseJSON->data = $usuario;
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

    public function GetEmpresas(Request $request){
      
        Log::info('[API][GetEmpresas]');

        Log::info("[API][GetEmpresas] Método Recibido: ". $request->getMethod());


        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');
            
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
            
            $tipo_usuario = $request->input('tipo_usuario');
            $id_responsable = $request->input('id_usuario');

            Log::info("[API][EmpresaPost] Tipo de Usuario: ". $tipo_usuario);
            Log::info("[API][EmpresaPost] ID Responsable: ". $id_responsable);
            
            $usuario = Empresa::getDespachos($tipo_usuario, $id_responsable);


            Log::info($usuario);
    
            if(count($usuario)>0){

                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDsuccess'), count($usuario));
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
    
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.BDempty'), count($usuario));
                $responseJSON->data = [];
                return json_encode($responseJSON);
    
            }
                
        } else {
            abort(404);
        }
    }

    public function GetDocumentos(Request $request){
      
        Log::info('[API][GetDocumentos]');

        Log::info("[API][GetDocumentos] Método Recibido: ". $request->getMethod());


        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            
            $tipo_usuario = $request->input('tipo_usuario');
            $id_usuario = $request->input('id_usuario');

            Log::info("[API][EmpresaPost] Tipo de Usuario: ". $tipo_usuario);
            Log::info("[API][EmpresaPost] ID Usuario: ". $id_usuario);
            
            $usuario = Documentos::getDocumentos($tipo_usuario, $id_usuario);

            Log::info($usuario);
    
            if(count($usuario)>0){

                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDsuccess'), count($usuario));
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
    
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.BDempty'), count($usuario));
                $responseJSON->data = [];
                return json_encode($responseJSON);
    
            }
                
        } else {
            abort(404);
        }
    }

    public function Valoracion(Request $request){
      
        Log::info('[API][Valoracion]');

        Log::info("[API][Valoracion] Método Recibido: ". $request->getMethod());


        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');
            
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
            
            $id_servicios = $request->input('id_servicios');
            $id_usuario = $request->input('id_usuario');
            $rating = $request->input('rating');

            Log::info("[API][Valoracion] ID Servicios: ". $id_servicios);
            Log::info("[API][Valoracion] ID Usuario: ". $id_usuario);
            Log::info("[API][Valoracion] Rating: ". $rating);
        
                
            $usuario = ValoracionUsuario::valoracionPost($id_servicios, $id_usuario, $rating);
            Log::info($usuario);
    
            if($usuario[0]->save == 1){

                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), count($usuario));
                $responseJSON->data = $usuario;
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
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');

            $celular = $request->input('celular');
            $celAbogado = $request->input('celAbogado');
            Log::info('[APIUsuarios][SMSConfirm] Celular: ' . $celular);
            Log::info('[APIUsuarios][SMSConfirm] Celular Abogado: ' . $celAbogado);

            $sms = new SMS();
            $status = $sms->enviarMensaje('El número de contacto de tu abogado es: ' . $celAbogado,'+52'. $celular);
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
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');

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

    public function GetServices(Request $request) {
     
        Log::info('[API][GetLaGetServices]');

        Log::info("[API][GetServices] Método Recibido: ". $request->getMethod());

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
            $tUsuario = $request->input('tUsuario');
            $id_user = $request->input('id_user');
            $status = $request->input('status');

            Log::info("[API][GetServices] Token: ". $token);
            Log::info("[API][GetServices] Tipo de Usuario: ". $tUsuario);
            Log::info("[API][GetServices] ID User: ". $id_user);
            Log::info("[API][GetServices] Status: ". $status);

            // $id_usuarios = $token_decrypt["usr"]->id_usuarios;   
            $usuario = Servicios::getServices($tUsuario, $id_user, $status);
        
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

    public function ChangeStatusServicio(Request $request){
  
        Log::info('[APIUsuarios][ChangeStatusServicio]');

        Log::info("[APIUsuarios][ChangeStatusServicio] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            
            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');
            

            $this->validate($request, [
                'token' => 'required',
              ]);

            $token = $request->input('token');
            $id_servicio = $request->input('id_servicio');
            $status = $request->input('status');
            $startTime = $request->input('startTime');
            $endTime = $request->input('endTime');
            $email = 'info@boogapp.mx';

            Log::info('[APIUsuarios][ChangeStatusServicio] Token: ' . $token);
            Log::info('[APIUsuarios][ChangeStatusServicio] ID Servicio: ' . $id_servicio);
            Log::info('[APIUsuarios][ChangeStatusServicio] Status: ' . $status);
            Log::info('[APIUsuarios][ChangeStatusServicio] Email: ' . $email);
            Log::info('[APIUsuarios][ChangeStatusServicio] StartTime: ' . $startTime);
            Log::info('[APIUsuarios][ChangeStatusServicio] EndTime: ' . $endTime);
            Log::info('[APIUsuarios][ChangeStatusServicio] Email: ' . $email);

            $usuario = Servicios::changeStatusServicio($id_servicio, $status, $startTime, $endTime);
 
            Log::info($usuario);
            if($usuario == 1){

                if ( $status === '2' ) {

                    $data['name'] = '';
                    //Send to queue email list of administrator mail
                    $data['user_id'] = '1';
                    $data['to'] = $email;
                    $data['priority'] = '2';
                    $data['tipo'] = 'Servicio';
                    $data['subject'] = 'Respuesta de servicio';
                    $data['body'] = "El abogado de tu elección ha aceptado el servicio solicitado. Al hacerlo, se compromete a otorgarte un <strong>20% de descuento</strong> sobre el monto total de los honorarios generados al finalizar el servicio. <br><br> ¡Recuerda exigir tu descuento al finalizar el mismo!";
                    // $data['subject'] = Lang::get('messages.emailSubscribeSubject');
                    //$data['priority'] = 1;
                    $mail = new QueueMails($data);
                    $mail->customMailUnique();
                } else if ( $status === '1' ) {

                    $data['name'] = '';
                    //Send to queue email list of administrator mail
                    $data['user_id'] = '1';
                    $data['to'] = $email;
                    $data['priority'] = '2';
                    $data['tipo'] = 'Servicio';
                    $data['subject'] = 'Respuesta de servicio';
                    $data['body'] = "El abogado ha rechazado tu solicitud de servicio";
                    // $data['subject'] = Lang::get('messages.emailSubscribeSubject');
                    //$data['priority'] = 1;
                    $mail = new QueueMails($data);
                    $mail->customMailUnique();
                } else if ( $status === '2.5' ) {

                    $data['name'] = '';
                    //Send to queue email list of administrator mail
                    $data['user_id'] = '1';
                    $data['to'] = $email;
                    $data['priority'] = '2';
                    $data['tipo'] = 'Servicio';
                    $data['subject'] = 'Respuesta de servicio';
                    $data['body'] = "El pago se ha relizado con éxito. <br> <strong>Resúmen de pago:</strong> <br> Total pagado: $500 MXN <br> Concepto: Match por servicio de abogado";
                    // $data['subject'] = Lang::get('messages.emailSubscribeSubject');
                    //$data['priority'] = 1;
                    $mail = new QueueMails($data);
                    $mail->customMailUnique();
                }

                Log::info('[APIUsuarios][ChangePassword] Se actualizo los datos de la moto en la tabla Motos');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsChangePass'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";
        }
    }

    public function ChangeActivo(Request $request){
  
        Log::info('[API][ChangeActivoLaw]');

        Log::info("[API][ChangeActivoLaw] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            
            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');
            

            $this->validate($request, [
                'token' => 'required',
              ]);

            $token = $request->input('token');
            $tipo_usuario = $request->input('tipo_usuario');
            $id_usuario = $request->input('id_usuario');
            $status = $request->input('status');
            $cuenta = $request->input('cuenta');

            Log::info('[API][ChangeActivoLaw] Token: ' . $token);
            Log::info('[API][ChangeActivoLaw] Tipo Usuario: ' . $tipo_usuario);
            Log::info('[API][ChangeActivoLaw] ID Usuario: ' . $id_usuario);
            Log::info('[API][ChangeActivoLaw] Status: ' . $status);
            Log::info('[API][ChangeActivoLaw] Cuenta: ' . $cuenta);

            if( $tipo_usuario == '0' ) {
                $usuario = Usuarios::changeActivoLaw($id_usuario, $status);
            } else if( $tipo_usuario == '1' ) {
                $usuario = Abogado::changeActivoLaw($id_usuario, $status, $cuenta);
            }
            if( $cuenta == '2') {
                $usuario2 = Empresa::changeActivoLaw($id_usuario, $tipo_usuario, $status);
            }
 
            Log::info($usuario);
            // Log::info($usuario2);
            if($usuario == 1 ) {

                Log::info('[API][ChangeActivoLaw] El abogado ha cambiado de actividad');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsActivo'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";
        }
    }

    public function ChangeHeadHunterLaw(Request $request){
  
        Log::info('[API][ChangeHeadHunterLaw]');

        Log::info("[API][ChangeHeadHunterLaw] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            
            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');
            

            $this->validate($request, [
                'token' => 'required',
              ]);

            $token = $request->input('token');
            $id_usuario = $request->input('id_usuario');
            $status = $request->input('status');

            Log::info('[API][ChangeHeadHunterLaw] Token: ' . $token);
            Log::info('[API][ChangeHeadHunterLaw] ID Usuario: ' . $id_usuario);
            Log::info('[API][ChangeHeadHunterLaw] Status: ' . $status);

            $usuario = Abogado::changeHeadHunterLaw($id_usuario, $status);
 
            Log::info($usuario);
            // Log::info($usuario2);
            if($usuario == 1 ) {

                Log::info('[API][ChangeActivoLaw] El abogado ha cambiado de actividad');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsActivo'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";
        }
    }

    public function GetActivo(Request $request){
  
        Log::info('[API][GetActivo]');

        Log::info("[API][GetActivo] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            
            header('Access-Control-Allow-Origin: *');
            // header('Access-Control-Allow-Methods: *');
            // header('Access-Control-Allow-Headers: *');
            
            /*
            $this->validate($request, [
                'token' => 'required',
              ]);
            */

            // $token = $request->input('token');
            $tipo_usuario = $request->input('tipo_usuario');
            $id_usuario = $request->input('id_usuario');

            // Log::info('[API][GetActivo] Token: ' . $token);
            Log::info('[API][GetActivo] Tipo Usuario: ' . $tipo_usuario);
            Log::info('[API][GetActivo] ID Usuario: ' . $id_usuario);

            if( $tipo_usuario == '0' ) {
                $usuario = Usuarios::getActivoLaw($id_usuario);
            } else if( $tipo_usuario == '1' ) {
                $usuario = Abogado::getActivoLaw($id_usuario);
            }
 
            Log::info($usuario);
            if(count($usuario)>0){

                Log::info('[API][GetActivo] El abogado ha cambiado de actividad');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.BDempty'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";
        }
    }

    public function ChangePassword(Request $request){
  
        Log::info('[API][ChangePassword]');

        Log::info("[API][ChangePassword] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            /*
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');
            */

            $this->validate($request, [
                'celular' => 'required',
                'password' => 'required'
              ]);

            Log::info('[API][ChangePassword] Conectado');
            $usuario = $request->input('usuario');
            $celular = $request->input('celular');
            $password = $request->input('password');

            if( $usuario == '0' ) {
                $usuario = Usuarios::changePassword($celular,$password);
            } else if( $usuario == '1' ) {
                $usuario = Abogado::changePassword($celular,$password);
            }
  
            Log::info($usuario);
            if($usuario == 1){

                Log::info('[API][ChangePassword] Se actualizo la contraseña');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsChangePass'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";
        }
    }

}