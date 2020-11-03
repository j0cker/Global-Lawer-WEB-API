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

class Documentos extends Model
{
    public $table = 'documentos';
    public $timestamps = true;
    //protected $dateFormat = 'U';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    //public $attributes;

    public function scopeUploadImg( $query, $id_usuarios, $id_tipo_usuarios, $id_empresa, $id_imagen, $img ){

        Log::info("[Abogado][scopeCreateUser]");

        $usuarios = new Documentos();

        $usuarios->id_usuarios = $id_usuarios;
        $usuarios->id_tipo_usuarios = $id_tipo_usuarios;
        $usuarios->id_empresa = $id_empresa;
        $usuarios->id_imagen = $id_imagen;
        $usuarios->img = $img;
        

        $obj = Array();
        $obj[0] = new \stdClass();
        $obj[0]->save = $usuarios->save(); //return true in the other one return 1
        $obj[0]->id = $usuarios->id;

        return $obj;
    }

    public function scopeGetDocumentos($query, $tipo_usuario, $id_usuario){

        Log::info("[Usuarios][scopeGetDespachos]");
  
        // $pass = hash("sha256", $pass);
  
  
        //activar log query
        DB::connection()->enableQueryLog();
  
        $sql = $query->selectRaw(
            'documentos.id_imagen'
        )
        ->where([
        ['id_usuarios', '=', $id_usuario],
        ['id_tipo_usuarios', '=', $tipo_usuario],
        ])->get();

  
        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);
  
        return $sql;
      }

}
?>
