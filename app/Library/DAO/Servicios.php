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

class Servicios extends Model
{
    public $table = 'servicios';
    public $timestamps = true;
    //protected $dateFormat = 'U';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    //public $attributes;

    public function scopeGetServicio($query, $id_abogado){

        Log::info("[Abogado][scopeGetServicio]");

        // $pass = hash("sha256", $pass);


        //activar log query
        DB::connection()->enableQueryLog();

        $sql = $query->where([
          ['id_abogado', '=', $id_abogado],
        ])->get();

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;

    }

    public function scopeGetServices($query, $tUsuario, $id_user){

      Log::info("[Abogado][scopeGetServices]");

      // $pass = hash("sha256", $pass);

      if ( $tUsuario == 0 ){
        //activar log query
        DB::connection()->enableQueryLog();
          
        $sql = $query->where([
          ['id_usuarios', '=', $id_user],
        ])->get();

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;

      } else if ( $tUsuario == 1 ) {
        //activar log query
        DB::connection()->enableQueryLog();
  
        $sql = $query->where([
          ['id_abogado', '=', $id_user],
        ])->get();
  
        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);
  
        return $sql;
      }


      
  }

    public function scopeServicePost($query, $id_abogado, $id_usuario, $payment){

      Log::info("[Usuarios][scopeCreateUser]");

      $usuarios = new Servicios();

      $usuarios->id_abogado = $id_abogado;
      $usuarios->id_usuarios = $id_usuario;
      $usuarios->precio = $payment;

      $obj = Array();
      $obj[0] = new \stdClass();
      $obj[0]->save = $usuarios->save(); //return true in the other one return 1
      $obj[0]->id = $usuarios->id;

      return $obj;
  }


}
?>