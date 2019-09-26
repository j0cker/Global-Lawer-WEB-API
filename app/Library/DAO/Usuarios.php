<?php

namespace App\Library\DAO;
use Config;
use App;
use Log;
use Illuminate\Database\Eloquent\Model;

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


    public function scopeLookForByEmailAndPass($query, $email, $pass)
    {

        Log::info("[Usuarios][scopeLookForByEmailAndPass]");

        $pass = hash("sha256", $pass);

        Log::info("[Usuarios][scopeLookForByEmailAndPass] pass: ". $pass);

        return $query->where([
          ['correo', '=', $email],
          ['pass', '=', $pass],
        ]);

    }

    public function scopeCreateUser($query, $nombre, $apellido, $correo, $telefono, $cel, $pass){

        Log::info("[Usuarios][scopeCreateUser]");

        $usuarios = new Usuarios();

        $usuarios->nombre = $nombre;
        $usuarios->apellido = $apellido;
        $usuarios->correo = $correo;
        $usuarios->cargo = '';
        $usuarios->telefono_fijo = $telefono;
        $usuarios->celular = $cel;
        $usuarios->pass = hash("sha256", $pass);

        $obj = Array();
        $obj[0] = new \stdClass();
        $obj[0]->save = $usuarios->save(); //return true in the other one return 1
        $obj[0]->id = $usuarios->id;

        return $obj;
    }

}
?>
