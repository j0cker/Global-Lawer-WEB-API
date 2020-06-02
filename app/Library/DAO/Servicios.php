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

    public function scopeGetServices($query, $tUsuario, $id_user, $status){

      Log::info("[Abogado][scopeGetServices]");

      // $pass = hash("sha256", $pass);

      if ( $tUsuario == 0 ){
        //activar log query
        DB::connection()->enableQueryLog();
          
        $sql = $query->where([
          ['id_usuarios', '=', $id_user],
          ['status', '=', $status],
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
          ['status', '=', $status],
        ])->get();
  
        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);
  
        return $sql;
      }


      
    }

    public function scopeServicePost($query,$id_servicios, $id_abogado, $payment, $tipo_servicio, $status){

      Log::info("[Servicios][scopeServicePost]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_servicios', '=' , $id_servicios]
        ])->update([
          'precio' => $payment,
          'tipo_servicio' => $tipo_servicio,
          'status' => $status
        ]);

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;
    }

    public function scopeChangeStatusServicio($query, $id_servicio, $status){
      
      Log::info("[Servicios][scopeChangeStatusServicio]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_servicios', '=' , $id_servicio]
        ])->update([
          'status' => $status
        ]);

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;

    }

}
?>
