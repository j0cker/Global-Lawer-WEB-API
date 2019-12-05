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

        $sql = $query->get();

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

    public function scopeCreateUser($query, $correo, $password, $cedula, $nombre, $apellido, $disponibilidad, $celular, $idiomas, $diasLaborales, $hEntrada, $hSalida, $address, $long, $lat, $escuela, $carrera, $mesTermino, $anoTermino){

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
        $usuarios->mesTermino = $mesTermino;
        $usuarios->anoTermino = $anoTermino;
        

        $obj = Array();
        $obj[0] = new \stdClass();
        $obj[0]->save = $usuarios->save(); //return true in the other one return 1
        $obj[0]->id = $usuarios->id;

        return $obj;
    }

}
?>
