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

class Usuarios extends Model
{
    public $table = 'usuarios';
    public $timestamps = true;
    //protected $dateFormat = 'U';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    //public $attributes;

    public function scopeUpdateIdDispositivo($query, $id_usuario,$idDispositivo){
      
      Log::info("[Abogado][scopeUpdateIdDispositivo]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_usuarios', '=' , $id_usuario]
        ])->update([
          'id_dispositivo' => $idDispositivo
        ]);

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;

    }

    public function scopeLookForByEmailAndPass($query, $email, $pass){

        Log::info("[Usuarios][scopeLookForByEmailAndPass]");

        $pass = hash("sha256", $pass);

        Log::info("[Usuarios][scopeLookForByEmailAndPass] pass: ". $pass);

        return $query->where([
          ['correo', '=', $email],
          ['pass', '=', $pass],
        ]);

    }

    public function scopeGetProfile($query, $id_user){

        Log::info("[Usuarios][scopeGetProfile]");

        // $pass = hash("sha256", $pass);


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

    }

    public function scopeGetProfile2($query, $cedula){

      Log::info("[Abogado][scopeGetProfile2]");

      // $pass = hash("sha256", $pass);


      //activar log query
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_usuarios', '=', $cedula],
      ])->get();

      //log query
      $queries = DB::getQueryLog();
      $last_query = end($queries);
      Log::info($last_query);

      return $sql;

    }

    public function scopeCreateUser($query, $nombre, $apellido, $correo, $telefono, $cel, $pass){

        Log::info("[Usuarios][scopeCreateUser]");

        $usuarios = new Usuarios();

        $usuarios->nombre = $nombre;
        $usuarios->apellido = $apellido;
        $usuarios->correo = $correo;
        $usuarios->telefono_fijo = $telefono;
        $usuarios->celular = $cel;
        $usuarios->pass = hash("sha256", $pass);

        $obj = Array();
        $obj[0] = new \stdClass();
        $obj[0]->save = $usuarios->save(); //return true in the other one return 1
        $obj[0]->id = $usuarios->id;

        return $obj;
    }

    public function scopeUpdateUser( $query, $id_usuarios, $acercaDe, $nombre, $apellido ){

      Log::info("[Usuarios][scopeUpdateUser]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_usuarios', '=' , $id_usuarios]
        ])->update([
          'acerca_de' => $acercaDe,
          'nombre' => $nombre,
          'apellido' => $apellido
        ]);

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;
    }

    public function scopeChangeActivoLaw($query, $id_usuarios, $status){
      
      Log::info("[Usuarios][scopeChangeActivoLaw]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_usuarios', '=' , $id_usuarios]
        ])->update([
          'activo' => $status
        ]);

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;

    }

    public function scopeGetActivoLaw($query, $id_usuario){

      Log::info("[Usuarios][scopeGetActivoUser]");
      Log::info("[Usuarios][scopeGetActivoUser] ID Usuario:" .  $id_usuario);

      // $pass = hash("sha256", $pass);


      //activar log query
      DB::connection()->enableQueryLog();

      $sql = $query->select('activo')
      ->where([
        ['id_usuarios', '=', $id_usuario]
      ])->get();


      //log query
      $queries = DB::getQueryLog();
      $last_query = end($queries);
      Log::info($last_query);

      return $sql;

    }

    public function scopeChangePassword($query, $celular,$password){
      
      Log::info("[Usuarios][scopeChangePassword]");
      DB::connection()->enableQueryLog();

      $pass = hash("sha256", $password);

      $sql = $query->where([
        ['celular', '=' , $celular]
        ])->update([
          'pass' => $pass
        ]);

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;

    }

    public function scopeLookForByCel($query, $celular){

      Log::info("[Usuarios][scopeLookForByCel]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['celular', '=', $celular]
      ]);

      $queries = DB::getQueryLog();
      $last_query = end($queries);
      Log::info($last_query);

      return $sql;

    }

}
?>
