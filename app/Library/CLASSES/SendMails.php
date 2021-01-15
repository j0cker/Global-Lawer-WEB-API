<?php
namespace App\Library\CLASSES;
use Illuminate\Support\Facades\Log;
use Mail;
use App;
use Config;
use Lang;

/*
Clase que envía correos a través del cronjob de MailsLauncher get Response
*/

class SendMails
{   private $data;

    public function __construct($data = []){
        Log::info("[sendMails][constructor]");
        $this->data = $data;
    }
    public function welcome(){
        Log::info("[sendMails][welcome][send]");
        $data = $this->data;
        Mail::send('emails.welcome', $data, function($message) use ($data)
        {
            $message->from(Config::get('mail.from.address'), Config::get('app.name'));
            $message->subject(Lang::get('messages.emailWelcome'));
            $message->to($data['email']);
        });
    }
    public function reset(){
        Log::info("[sendMails][reset][send]");
        $data = $this->data;
        Mail::send('emails.reset', $data, function($message) use ($data)
        {
            $message->from(Config::get('mail.from.address'), Config::get('app.name'));
            $message->subject(Lang::get('messages.emailReset'));
            $message->to($data['email']);
        });
    }
    /*
    public function verificationCompare(){
        Log::info("[sendMails][verificationCompare]");
        $data = $this->data;
        $verification_code = str_random(20);
        $email=$data["mail_previous"];
        $ClientesVer = App\Library\dao\Clientes::updateVerify($verification_code);
        Log::info('[sendMails][saveProfile][Post][All][WithoutPass] emailBD: '.$email.' emailPost: '.$data["email"].'');
        if($email!=$data["email"]){
            if($ClientesVer==1){
                $data["verification_code"] = $verification_code;
                Log::info("[sendMails][verificationCompare][send]");
                Mail::send('emails.verification', $data, function($message) use ($data)
                {
                    $message->from(Config::get('mail.from.address'), Config::get('app.name'));
                    $message->subject(Lang::get('messages.emailVerification'));
                    $message->to($data['mail_previous']);
                });
                Mail::send('emails.verification', $data, function($message) use ($data)
                {
                    $message->from(Config::get('mail.from.address'), Config::get('app.name'));
                    $message->subject(Lang::get('messages.emailVerification'));
                    $message->to($data['email']);
                });

            } else {
                Log::info("[sendMails][verificationCompare][no send]");
            }
        } else {
            Log::info("[sendMails][verificationCompare][no send]");
        }
    }
    */
    public function newPassword(){
        Log::info("[sendMails][newPassword][send]");
        $data = $this->data;
        Mail::send('emails.password', $data, function($message) use ($data)
        {
            $message->from(Config::get('mail.from.address'), Config::get('app.name'));
            $message->subject(Lang::get('messages.emailReset'));
            $message->to($data['email']);
        });
    }
    public function singleMailUnique(){
        Log::info("[sendMails][singleMailUnique][send]");
        $data = $this->data;
        Mail::send('emails.custom', $data, function($message) use ($data)
        {
            $message->from(Config::get('mail.from.address'), Config::get('app.name'));
            $message->subject($data['subject']);
            $message->to($data['email']);
        });
    }
    public function customMail(){
        Log::info("[sendMails][customMail][send]");
        $data = $this->data;
        try {
          Mail::send('emails.custom', $data, function($message) use ($data)
          {
            $message->from(Config::get('mail.from.address'), Config::get('app.name'));
            $message->subject($data['subject']);
            $message->to($data['email']);
          });
        } catch(\Exception $e){
          Log::error("[sendMails][customMail][send] ERROR: ".$e." ".$data['email']."");
        }
    }

}