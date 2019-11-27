<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
**** End Points Generales ****
*/
 
//Ingresar User
Route::get('/ingresar', 'API@Ingresar');

/* 
**** End Points Normal User ****
*/

// Registro usuarios normales
Route::get('/normal_user/registrar', 'APIUserNormal@Registrar');
// Verificación de cedula profesional
Route::get('/normal_user/verifyCedula', 'APIUserNormal@VerifyCedula');

/* 
**** End Points Abogados ****
*/
// Registro usuarios normales
Route::get('/lawyer/registrar', 'APILawyer@Registrar');
// Show de abogados
Route::get('/lawyer/abogadosCards', 'APILawyer@AbogadosCards');
