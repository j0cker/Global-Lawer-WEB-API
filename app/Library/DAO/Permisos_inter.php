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
    const UPDATED_AT = null;
    //public $attributes;


    public function scopeLookForByIdUsuarios($query, $idUsuarios)
    {

        Log::info("[Permisos_inter][scopeLookForByIdUsuarios] idUsuarios: ". $idUsuarios);

        return $query->select('id_permisos')->where([
          ['id_usuarios', '=', $idUsuarios],
        ]);

    }

    public function scopeLookForByIdLawyer($query, $idUsuarios)
    {

        Log::info("[Permisos_inter][scopeLookForByIdUsuarios] idUsuarios: ". $idUsuarios);

        return $query->select('id_permisos')->where([
          ['id_usuarios', '=', $idUsuarios],
        ]);

    }

    public function scopeCreatePermisoInter($query, $id_usuarios){

      Log::info("[Permisos_inter][scopeCreatePermisoInter]");

      $permisos_inter = new Permisos_inter();

      $permisos_inter->id_usuarios = $id_usuarios;
      $permisos_inter->id_permisos = 1;

      $obj = Array();
      $obj[0] = new \stdClass();
      $obj[0]->save = $permisos_inter->save(); //return true in the other one return 1
      $obj[0]->id = $permisos_inter->id;

      return $obj;
    }
}
?>
