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

class ValoracionAbogado extends Model
{
    public $table = 'valoracion_abogado';
    public $timestamps = true;
    //protected $dateFormat = 'U';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    //public $attributes;

    public function scopeValoracionPost($query, $id_servicios, $id_usuario, $rating){

        Log::info("[ValoracionAbogado][scopeValoracionPost]");
  
        $usuarios = new ValoracionUsuario();
  
        $usuarios->id_servicios = $id_servicios;
        $usuarios->id_abogado = $id_abogado;
        $usuarios->id_usuarios = $id_usuario;
        $usuarios->rating_abogado = $rating;
  
        $obj = Array();
        $obj[0] = new \stdClass();
        $obj[0]->save = $usuarios->save(); //return true in the other one return 1
        $obj[0]->id = $usuarios->id;
  
        return $obj;
      }

}
?>
