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

    public function scopeCreateEmpresaLaw($query, $tipo_usuario, $id_responsable, $nombre, $corrreoResponsable, $telefono, $giro, $tiempoConstitucion, $servicio, $acercaDe){

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
        $usuarios->correo = $corrreoResponsable;
        $usuarios->telefono = $telefono;
        $usuarios->giro = $giro;
        $usuarios->constitucion = $tiempoConstitucion;
        $usuarios->servicios = $servicio;
        $usuarios->acercaDe = $acercaDe;

        $obj = Array();
        $obj[0] = new \stdClass();
        $obj[0]->save = $usuarios->save(); //return true in the other one return 1
        $obj[0]->id = $usuarios->id;
  
        return $obj;
    }

    public function scopeChangeActivoLaw($query, $id_usuario, $tipo_usuario, $status){
      
      Log::info("[Servicios][scopeChangeActivoLaw]");
      DB::connection()->enableQueryLog();

      if( $status == '1' ){
        $status2 = '0';
      } else if ( $status == '0') {
        $status2 = '1';
      }

      // Log::info("[Servicios][scopeChangeActivoLaw] Status 2: " . $status2);
      if ( $tipo_usuario == '0') {

        $sql = $query->where([
          ['id_usuarios', '=' , $id_usuario]
          ])->update([
            'activo' => $status2
          ]);

      } else if ( $tipo_usuario == '1') {

        $sql = $query->where([
          ['id_abogado', '=' , $id_usuario]
          ])->update([
            'activo' => $status2
          ]);

      }

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;

    }

    public function scopeDespachosCards($query){

      Log::info("[Usuarios][scopeDespachosCards]");

      // $pass = hash("sha256", $pass);


      //activar log query
      DB::connection()->enableQueryLog();

      $sql = $query->leftJoin('abogado', 'abogado.id_abogado', '=', 'empresa.id_abogado')
        ->selectRaw(
          'empresa.*,
          abogado.id_dispositivo AS idDispositivoAbo'
        )
        ->where([
        ['empresa.id_usuarios', '=', '0'],
        ['empresa.activo', '=', '1']
      ])->get();

      //log query
      $queries = DB::getQueryLog();
      $last_query = end($queries);
      Log::info($last_query);

      return $sql;
    }

    public function scopeGetDespachos($query, $tipo_usuario, $id_usuario){

      Log::info("[Usuarios][scopeGetDespachos]");

      // $pass = hash("sha256", $pass);


      //activar log query
      DB::connection()->enableQueryLog();

      if($tipo_usuario == '0') {
        $sql = $query->where([
          ['id_usuarios', '=', $id_usuario],
        ])->get();
      } else if($tipo_usuario == '1') {
        $sql = $query->where([
          ['id_abogado', '=', $id_usuario],
        ])->get();
      }


      //log query
      $queries = DB::getQueryLog();
      $last_query = end($queries);
      Log::info($last_query);

      return $sql;
    }

}
?>
