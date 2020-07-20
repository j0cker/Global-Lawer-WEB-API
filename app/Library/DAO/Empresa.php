<?php

namespace App\Library\DAO;
use Config;
use App;
use Log;
use Illuminate\Database\Eloquent\Model;
use DB;

/*
update and insert doesnt need get->()
*/

class Empresa extends Model
{
    public $table = 'empresa';
    public $timestamps = true;
    //protected $dateFormat = 'U';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    //public $attributes;

    public function scopeCreateEmpresa($query, $tipo_usuario, $id_responsable, $nombre, $giro){

        Log::info("[Empresa][scopeEmpresaPost]");
  
        $usuarios = new Empresa();

        // Usuario = 0
        if ( $tipo_usuario === '0' ) {
          $usuarios->id_usuarios = $id_responsable;
          // Abogado = 1
        } else if ( $tipo_usuario === '1') {
          $usuarios->id_abogado = $id_responsable;
        }
  
        $usuarios->nombre_empresa = $nombre;
        $usuarios->giro = $giro;
  
        $obj = Array();
        $obj[0] = new \stdClass();
        $obj[0]->save = $usuarios->save(); //return true in the other one return 1
        $obj[0]->id = $usuarios->id;
  
        return $obj;
      }

}
?>
