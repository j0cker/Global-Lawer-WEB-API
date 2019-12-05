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

// Prueba SMS
Route::get('/enviarsms', 'API@SMS');
Route::get('/verifyCode', 'API@VerificarSMS');

/* 
**** End Points Normal User ****
*/

// Registro usuarios normales
Route::get('/normal_user/registrar', 'APIUserNormal@Registrar');
// Verificación de cedula profesional
Route::get('/normal_user/verifyCedula', 'APIUserNormal@VerifyCedula');
// Get Perfil abogados
Route::get('/normal_user/getProfile', 'APIUserNormal@GetProfile');

/* 
**** End Points Abogados ****
*/
// Registro usuarios normales
Route::get('/lawyer/registrar', 'APILawyer@Registrar');

// Show de abogados
Route::get('/lawyer/abogadosCards', 'APILawyer@AbogadosCards');

// Get Perfil abogados
Route::get('/lawyer/getProfile', 'APILawyer@GetProfile');