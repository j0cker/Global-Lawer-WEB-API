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

    public function scopeUpdateIdDispositivo($query, $id_usuario,$idDispositivo){
      
      Log::info("[Abogado][scopeUpdateIdDispositivo]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_abogado', '=' , $id_usuario]
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

        $sql = $query->leftJoin('servicios', 'servicios.id_abogado', '=', 'abogado.id_abogado')
        ->selectRaw(
          'abogado.*,
          COUNT(servicios.id_servicios) AS countServ'
        )
        ->where([
          ['abogado.activo', '=', '1'],
          ['abogado.visible', '=', '1']
        ])
        ->groupBy('abogado.id_abogado')
        ->get();

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

    public function scopeGetProfile2($query, $tipo, $cedula){

        Log::info("[Abogado][scopeGetProfile2]");

        // $pass = hash("sha256", $pass);


        //activar log query
        DB::connection()->enableQueryLog();

        if( $tipo == 0) {

          $sql = $query->where([
            ['id_abogado', '=', $cedula],
          ])->get();
  
          //log query
          $queries = DB::getQueryLog();
          $last_query = end($queries);
          Log::info($last_query);

        } else if( $tipo == 1) {

          $sql = $query->where([
            ['cedula', '=', $cedula],
          ])->get();
  
          //log query
          $queries = DB::getQueryLog();
          $last_query = end($queries);
          Log::info($last_query);

        }

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

    public function scopeUpdateLaw( $query, $id_abogado, $acercaDe, $nombre, $apellido, $disponibilidad ){

      Log::info("[Usuarios][scopeUpdateLaw]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_abogado', '=' , $id_abogado]
        ])->update([
          'acerca_de' => $acercaDe,
          'nombre' => $nombre,
          'apellido' => $apellido,
          'disponibilidad' => $disponibilidad
        ]);

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;
    }

    public function scopeUpdateLawAddress( $query, $id_abogado, $address ){

      Log::info("[Usuarios][scopeUpdateLaw]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_abogado', '=' , $id_abogado]
        ])->update([
          'address' => $address
        ]);

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;
    }

    public function scopeUpdateLawCedula( $query, $id_abogado, $cedula ){

      Log::info("[Usuarios][scopeUpdateLaw]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_abogado', '=' , $id_abogado]
        ])->update([
          'cedula' => $cedula
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

    public function scopeChangeHeadHunterLaw($query, $id_usuarios, $status){
      
      Log::info("[Abogado][scopeChangeActivoLaw]");
      DB::connection()->enableQueryLog();

      if($status == '1') {
        $sql = $query->where([
          ['id_abogado', '=' , $id_usuarios]
          ])->update([
            'headHunter' => 'headHunter'
          ]);
      } else if ($status == '0') {
        $sql = $query->where([
          ['id_abogado', '=' , $id_usuarios]
          ])->update([
            'headHunter' => 'nohd'
          ]);
      }
      
      //log query
      $queries = DB::getQueryLog();
      $last_query = end($queries);
      Log::info($last_query);

      return $sql;

    }

    public function scopeGetActivoLaw($query, $id_usuario){

      Log::info("[Abogado][scopeGetActivoLaw]");
      Log::info("[Abogado][scopeGetActivoLaw] ID Usuario:" .  $id_usuario);

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
    
    public function scopeUpdatePayment( $query, $id_abogado, $servicePayment, $amount ){

      Log::info("[Usuarios][scopeUpdateLaw]");
      DB::connection()->enableQueryLog();

      if( $servicePayment === '1'){

        $sql = $query->where([
          ['id_abogado', '=' , $id_abogado]
          ])->update([
            'cost_presencial' => $amount
          ]);
      } else if( $servicePayment === '2'){

        $sql = $query->where([
          ['id_abogado', '=' , $id_abogado]
          ])->update([
            'cost_online' => $amount
          ]);
      }

      
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
