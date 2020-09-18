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

class Abogado extends Model
{
    public $table = 'abogado';
    public $timestamps = true;
    //protected $dateFormat = 'U';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    //public $attributes;


    public function scopeLookForByEmailAndPass($query, $email, $pass){

        Log::info("[Abogado][scopeLookForByEmailAndPass]");

        $pass = hash("sha256", $pass);

        Log::info("[Abogado][scopeLookForByEmailAndPass] pass: ". $pass);

        return $query->where([
          ['correo', '=', $email],
          ['pass', '=', $pass],
        ]);

    }

    public function scopeAbogadosCards($query){

        Log::info("[Usuarios][scopeLookForByEmailAndPass]");

        // $pass = hash("sha256", $pass);


        //activar log query
        DB::connection()->enableQueryLog();

        $sql = $query->where([
          ['activo', '=', '1'],
          ['visible', '=', '1']
        ])->get();

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;
    }

    public function scopeGetProfile($query, $id_user){

        Log::info("[Abogado][scopeGetProfile]");

        // $pass = hash("sha256", $pass);


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

    public function scopeGetProfile2($query, $cedula){

        Log::info("[Abogado][scopeGetProfile2]");

        // $pass = hash("sha256", $pass);


        //activar log query
        DB::connection()->enableQueryLog();

        $sql = $query->where([
          ['cedula', '=', $cedula],
        ])->get();

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;

    }

    public function scopeCreateUser($query, $correo, $password, $cedula, $nombre, $apellido, $disponibilidad, $celular, $idiomas, $diasLaborales, $hEntrada, $hSalida, $address, $long, $lat, $escuela, $carrera, $anoTermino){

        Log::info("[Abogado][scopeCreateUser]");

        $usuarios = new Abogado();

        $usuarios->correo = $correo;
        $usuarios->pass = hash("sha256", $password);
        $usuarios->cedula = $cedula;
        $usuarios->nombre = $nombre;
        $usuarios->apellido = $apellido;
        $usuarios->disponibilidad = $disponibilidad;
        $usuarios->celular = $celular;
        $usuarios->idiomas = $idiomas;
        $usuarios->diasLaborales = $diasLaborales;
        $usuarios->hEntrada = $hEntrada;
        $usuarios->hSalida = $hSalida;
        $usuarios->address = $address;
        $usuarios->longitud = $long;
        $usuarios->latitud = $lat;
        $usuarios->escuela = $escuela;
        $usuarios->carrera = $carrera;
        $usuarios->anoTermino = $anoTermino;
        

        $obj = Array();
        $obj[0] = new \stdClass();
        $obj[0]->save = $usuarios->save(); //return true in the other one return 1
        $obj[0]->id = $usuarios->id;

        return $obj;
    }

    public function scopeUpdateLaw( $query, $id_abogado, $acercaDe, $nombre, $apellido ){

      Log::info("[Usuarios][scopeUpdateLaw]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_abogado', '=' , $id_abogado]
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

    public function scopeChangeActivoLaw($query, $id_usuarios, $status, $cuenta){
      
      Log::info("[Abogado][scopeChangeActivoLaw]");
      DB::connection()->enableQueryLog();

      if( $cuenta == '1' ) {
        $sql = $query->where([
          ['id_abogado', '=' , $id_usuarios]
          ])->update([
            'visible' => $status
          ]);

      } else if ( $cuenta == '2' ) {

        $sql = $query->where([
          ['id_abogado', '=' , $id_usuarios]
          ])->update([
            'activo' => $status
          ]);
      }

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;

    }

    public function scopeGetActivoLaw($query, $id_usuario){

      Log::info("[Contacto_emergencia][scopeGetImg]");
      Log::info("[Contacto_emergencia][scopeGetImg] ID Usuario:" .  $id_usuario);

      // $pass = hash("sha256", $pass);


      //activar log query
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_abogado', '=', $id_usuario]
      ])->select('activo')->get();


      //log query
      $queries = DB::getQueryLog();
      $last_query = end($queries);
      Log::info($last_query);

      return $sql;

    }

    public function scopeChangePassword($query, $celular,$password){
      
      Log::info("[Abogado][scopeChangePassword]");
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

    public function scopeAddServicios($query, $id_usuario, $servicio){
      
      Log::info("[Abogado][scopeChangePassword]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_abogado', '=' , $id_usuario]
        ])->update([
          'servicio' => $servicio
        ]);

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;

    }

}
?>
