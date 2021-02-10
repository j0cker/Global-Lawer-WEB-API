<?php

namespace App\Library\DAO;
use Config;
use App;

use Illuminate\Database\Eloquent\Model;

/*

update and insert doesnt need get->()


*/

class Queue_mails extends Model
{
    protected $table = 'queue_mails';
    public $timestamps = true;
    //protected $dateFormat = 'U';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function scopeAddMailQueue($query, $user_id, $plantilla, $toMail, $prioridad, $body, $subject, $name, $tipo, $VAR_1, $VAR_2)
    {   $queue_mails = new Queue_mails;
        $queue_mails->I_UID = $user_id;
        $queue_mails->N_PLANTILLA = $plantilla;
        $queue_mails->N_SEND_TO = $toMail;
        $queue_mails->I_PRIORIDAD = $prioridad;
        $queue_mails->body = $body;
        $queue_mails->subject = $subject;
        $queue_mails->name = $name;
        $queue_mails->q_tipo = $tipo;
        $queue_mails->VAR_1 = $VAR_1;
        $queue_mails->VAR_2 = $VAR_2;
        return $queue_mails->save();
    }

}
?>