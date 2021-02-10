<?php
namespace App\Library\CLASSES;
use Illuminate\Support\Facades\Log;
use Mail;
use App;
use Config;
use Lang;
use Auth;
use App\Library\DAO\Queue_mails;

/*
Clase que almacena correos electrónicos a la base de datos.

for the constructor:

//Send to queue email list of administrator mail
//$data["user_id"] = $clientes[0]->c_id;
//$data['email'] = Config::get('mail.from.address');
//$data['body'] = "".Lang::get('messages.emailSubscribeBody')."".$email."";
//$data['subject'] = Lang::get('messages.emailSubscribeSubject');
//$data['name'] = Config::get('mail.from.name');
//$data['priority'] = 1;

example:

$mail = new \App\Library\classes\queueMails($data);
$mail->reset();

params:

($user_id, $plantilla, $toMail, $prioridad, $body, $subject, $name, $tipo, $VAR_1, $VAR_2)

*/

class QueueMails
{   private $data;

    public function __construct($data = []){
        Log::info("[Mail][constructor]");
        $this->data = $data;
    }
    public function welcome(){
        Log::info("[Mail][welcome][queue]");
        $data = $this->data;
        $queue_mails = Queue_mails::addMailQueue($data['user_id'], 'emails.welcome', $data['email'], (int)Lang::get('messages.prioridadWelcome'), '', '', $data['name'], $data["tipo"], $data['password'], $data['verification_code']);
    }
    public function reset(){
      /*
      aparentemente reset y newpassword son lo mismo
      el proceso de restauración de la contraseña es manual hay que programar el DAO
      */
        Log::info("[Mail][reset][queue]");
        $data = $this->data;
        $queue_mails = Queue_mails::addMailQueue($data['user_id'], 'emails.reset', $data['email'], (int)Lang::get('messages.prioridadReset'), '', '' ,'', $data["tipo"], $data['password'], '');
    }
    public function verificationCompare(){
      /*
      This verification is for change the email of the user account
      */
        Log::info("[Mail][verificationCompare]");
        $data = $this->data;

        $queue_mails = Queue_mails::addMailQueue($data['user_id'], 'emails.verification', $data['email'], (int)Lang::get('messages.prioridadVerificationCompare'), $data['name'], $data['mail_previous'], '');
    }
    public function newPassword(){
      /*
      aparentemente reset y newpassword son lo mismo
      el proceso de restauración de la contraseña es manual hay que programar el DAO
      */
        Log::info("[Mail][newPassword]");
        $data = $this->data;

        $queue_mails = Queue_mails::addMailQueue($data['user_id'], 'emails.password', $data['email'], (int)Lang::get('messages.prioridadPswd'), '', '', $data['name'], $data["tipo"], $data['password'], '');
    }
    //single custom unique emails without name and userID
    public function singleMailUnique(){
       Log::info("[Mail][singleMailUnique]");
       $params = $this->data;
       $to = $params['to'];
       $subject = $params['subject'];
       $body = $params['body'];
       $priority = $params['priority'];
       $name = $params['name'];
       $queue_mails = Queue_mails::addMailQueue(0, 'emails.single', $to, $priority, $body, $subject, $name, $params["tipo"] ,'' ,'');

    }
    //custom unique emails function from wherever with names and User ID
    public function customMailUnique(){
       Log::info("[Mail][customMailUnique]");
       $params = $this->data;
       $to = $params['to'];
       $subject = $params['subject'];
       $body = $params['body'];
       $priority = $params['priority'];
       $name = $params['name'];
       $queue_mails = Queue_mails::addMailQueue($params['user_id'], 'emails.custom', $to, $priority, $body, $subject, $name, $params["tipo"] ,'' ,'');

    }
    /*
    //custom emails from admin panel
    public function customMail(){
       Log::info("[Mail][customMail]");
       $params = $this->data;
       $target = $params['target'];
       $subject = $params['subject'];
       $body = $params['body'];
       $priority = $params['priority'];
       if($target=="all"){
         Log::info("[Mail][customMail] all");
         $mailsToSend = Clientes::all();
       } else if($target=="subs"){
         Log::info("[Mail][customMail] subs");
         $mailsToSend = Bmsust::all();
       } else {
         Log::info("[Mail][customMail] tipo de paquetes");
         $mailsToSend = Clientes::getAllUsersTipo($target)->get();
       }
       $mailsToSend = json_decode($mailsToSend);
       if($mailsToSend){
           Log::info("[Mail][customMail] hay mails de éste tipo");
           foreach($mailsToSend as $item){
                if($target=="subs"){
                        $bmsmail = Queue_mails::addMailQueue(Auth::user()->id, 'emails.custom', $item->email, $priority, $body, $subject, 'Subscriber');
                } else {
                        $bmsmail = Queue_mails::addMailQueue(Auth::user()->id, 'emails.custom', $item->email, $priority, $body, $subject, $item->name);
                }
           }
       } else {
           Log::info("[Mail][customMail] no hay mails de éste tipo");
       }
    }
    */

}