<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Lang;
use App;
use Config;
use Auth;
use Log;
use App\Library\DAO\Queue_mails;
use App\Library\CLASSES\SendMails;
use App\Library\CLASSES\QueueMails;

/*
Lanzador de correos electrónicos por medio de Cronjobs Get responses
*/

class MailsLauncher extends Controller
{
  public function mailsLauncher(){
    Log::info('[MailsLauncher][mailsLauncher]');

    $bmsmails = Queue_mails::orderBy('I_prioridad', 'asc')->limit(100)->get();
    $bmsmails = json_decode($bmsmails);
    if($bmsmails){
      Log::info('[MailsLauncher][mailsLauncher] Hay Mails para enviar');
      echo "Hay Mails para enviar";
      $c_mails = 0;
      foreach($bmsmails as $mail){
        if($mail->N_PLANTILLA=="emails.welcome"){
          Log::info('[MailsLauncher][mailsLauncher] Sending Welcome');
          $data['name'] = $mail->name;
          $data['email'] = $mail->N_SEND_TO;
          $data['password'] = $mail->VAR_1;
          $data['verification_code'] = $mail->VAR_2;
          $mail_send = new SendMails($data);
          $mail_send->welcome();
          Queue_mails::where('I_MAIL', $mail->I_MAIL)->delete();
          $c_mails++;
        } else if($mail->N_PLANTILLA=="emails.reset"){
          Log::info('[MailsLauncher][mailsLauncher] Sending Reset');
          $data['email'] = $mail->N_SEND_TO;
          $data['password'] = $mail->VAR_1;
          $mail_send = new SendMails($data);
          $mail_send->reset();
          Queue_mails::where('I_MAIL', $mail->I_MAIL)->delete();
          $c_mails++;
        } else if($mail->N_PLANTILLA=="emails.verification"){
          Log::info('[MailsLauncher][mailsLauncher] Sending Verification');
          $data['email'] = $mail->N_SEND_TO;
          $data['name'] = $mail->name;
          $data['mail_previous'] = $mail->VAR2;
          $mail_send = new SendMails($data);
          $mail_send->verificationCompare();
          Queue_mails::where('I_MAIL', $mail->I_MAIL)->delete();
          $c_mails++;
        } else if($mail->N_PLANTILLA=="emails.password"){
          Log::info('[MailsLauncher][mailsLauncher] Sending Password');
          $data['name'] = $mail->name;
          $data['email'] = $mail->N_SEND_TO;
          $data['password'] = $mail->VAR_1;
          $mail_send = new SendMails($data);
          $mail_send->newPassword();
          Queue_mails::where('I_MAIL', $mail->I_MAIL)->delete();
          $c_mails++;
        } else if($mail->N_PLANTILLA=="emails.custom"){
          Log::info('[MailsLauncher][mailsLauncher] Sending Custom');
          $data['email'] = $mail->N_SEND_TO;
          $data['subject'] = $mail->subject;
          $data['body'] = $mail->body;
          $data['name'] = $mail->name;
          try {
            $mail_send = new SendMails($data);
            $mail_send->customMail();
          } catch(\Exception $e){
            Log::info('[MailsLauncher][mailsLauncher] ERROR: '.$e.' '.$data['email'].'');
          }
          Queue_mails::where('I_MAIL', $mail->I_MAIL)->delete();
          $c_mails++;
        } else if($mail->N_PLANTILLA=="emails.single"){
          Log::info('[MailsLauncher][mailsLauncher] Sending Single');
          $data['email'] = $mail->N_SEND_TO;
          $data['body'] = $mail->body;
          $data['subject'] = $mail->subject;
          $data['name'] = $mail->name;
          $mail_send = new SendMails($data);
          try {
            $mail_send->singleMailUnique();
          } catch(\Exception $e){
            Log::error("[MailsLauncher][mailsLauncher] Error: ".$e." ".$data['email']."");
          }
          Queue_mails::where('I_MAIL', $mail->I_MAIL)->delete();
          $c_mails++;
        }
      }//fin foreach
      Log::info("[MailsLauncher][mailsLauncher] Total enviados: ".$c_mails."");
      echo "<br />";
      echo "Total enviados: ".$c_mails."";
    } else {
      Log::info('[MailsLauncher][mailsLauncher] No hay Mails para enviar');
      echo "No hay Mails para enviar";
    }

  }
}