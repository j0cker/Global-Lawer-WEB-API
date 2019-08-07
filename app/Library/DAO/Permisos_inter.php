<?php

namespace App\Library\DAO;
use Config;
use App;
use Log;
use Illuminate\Database\Eloquent\Model;

/*

update and insert doesnt need get->()


*/

class Permisos_inter extends Model
{
    public $table = 'permisos_inter';
    public $timestamps = true;
    //protected $dateFormat = 'U';
    const CREATED_AT = 'created_at';
    //const UPDATED_AT = 'updated_at';
    //public $attributes;


    public function scopeLookForByIdUsuarios($query, $idUsuarios)
    {

        Log::info("[Permisos_inter][scopeLookForByIdUsuarios] idUsuarios: ". $idUsuarios);

        return $query->select('id_permisos')->where([
          ['id_usuarios', '=', $idUsuarios],
        ]);

    }
}
?>
