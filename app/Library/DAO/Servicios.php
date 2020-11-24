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
        //Usuario
        //activar log query
        DB::connection()->enableQueryLog();
          
        $sql = $query->leftJoin('abogado', 'abogado.id_abogado', '=', 'servicios.id_abogado')
        ->leftJoin('usuarios', 'usuarios.id_usuarios', '=', 'servicios.id_usuarios')
        ->leftJoin('empresa', 'empresa.id_empresa', '=', 'servicios.id_despacho')
        ->selectRaw(
          'servicios.*,
          CONCAT(abogado.nombre, " ", abogado.apellido) AS nombreCompletoAbo,
          CONCAT(usuarios.nombre, " ", usuarios.apellido) AS nombreCompletoUsr,
          abogado.id_dispositivo AS idDispositivoAbo,
          usuarios.id_dispositivo AS idDispositivoUsr,
          usuarios.correo AS correoUsr,
          abogado.correo AS correoAbo,
          abogado.cedula as cedulaAbo,
          empresa.nombre_empresa as nombreEmpresa'
      )
        ->where([
          ['servicios.id_usuarios', '=', $id_user],
          ['servicios.status', '=', $status],
        ])->get();

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;

      } else if ( $tUsuario == 1 ) {
        //Abogado
        //activar log query
        DB::connection()->enableQueryLog();
  
        $sql = $query->leftJoin('usuarios', 'usuarios.id_usuarios', '=', 'servicios.id_usuarios')
        ->leftJoin('abogado', 'abogado.id_abogado', '=', 'servicios.id_abogado')
        ->leftJoin('empresa', 'empresa.id_empresa', '=', 'servicios.id_empresa')
        ->selectRaw(
          'servicios.*,
          CONCAT(usuarios.nombre, " ", usuarios.apellido) AS nombreCompletoUsr,
          CONCAT(abogado.nombre, " ", abogado.apellido) AS nombreCompletoAbo,
          abogado.id_dispositivo AS idDispositivoAbo,
          usuarios.id_dispositivo AS idDispositivoUsr,
          usuarios.correo AS correoUsr,
          abogado.correo AS correoAbo,
          abogado.cedula as cedulaAbo,
          empresa.nombre_empresa as nombreEmpresa'
      )
        ->where([
          ['servicios.id_abogado', '=', $id_user],
          ['servicios.status', '=', $status],
        ])->get();
  
        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);
  
        return $sql;
      }


      
    }

    public function scopeServicePost($query, $id_abogado, $id_usuario, $id_despacho, $id_empresa, $servicio, $descripcion){

      Log::info("[Usuarios][scopeCreateUser]");

      $usuarios = new Servicios();

      $usuarios->id_abogado = $id_abogado;
      $usuarios->id_usuarios = $id_usuario;
      $usuarios->id_despacho = $id_despacho;
      $usuarios->id_empresa = $id_empresa;
      $usuarios->precio = '500';
      $usuarios->tipo_servicio = $servicio;
      $usuarios->descripcion = $descripcion;

      $obj = Array();
      $obj[0] = new \stdClass();
      $obj[0]->save = $usuarios->save(); //return true in the other one return 1
      $obj[0]->id = $usuarios->id;

      return $obj;
    }

    public function scopeServicePost2($query,$id_servicios, $id_abogado, $payment, $tipo_servicio, $status){

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

    public function scopeChangeStatusServicio($query, $id_servicio, $status, $startTime, $endTime){
      
      Log::info("[Servicios][scopeChangeStatusServicio]");
      DB::connection()->enableQueryLog();

      if($status === '3') {

        $sql = $query->where([
          ['id_servicios', '=' , $id_servicio]
          ])->update([
            'status' => $status,
            'startTime' => $startTime
          ]);
      } else {

        $sql = $query->where([
          ['id_servicios', '=' , $id_servicio]
          ])->update([
            'status' => $status
          ]);
      }


        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;

    }

    public function scopeGetServiciosUsuarios($query, $id_servicios){

      Log::info("[Servicios][scopeGetProfileById] id_servicios: " . $id_servicios);
      /* Aquí se ocupa 4 joins con left para traer la información de acuerdo a la relación de dichas tablas */
      DB::connection()->enableQueryLog();

      $sql = $query   ->leftJoin('usuarios', 'usuarios.id_usuarios', '=', 'servicios.id_usuarios')
                      ->where('servicios.id_servicios', '=', $id_servicios)
                      ->get();

      //log query
      $queries = DB::getQueryLog();
      $last_query = end($queries);
      Log::info($last_query);

      return $sql;

  }

}
?>
