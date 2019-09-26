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

//registro usuarios normales
Route::get('/normal_user/registrar', 'APIUserNormal@Registrar');

/* 
**** End Points Abogados ****
*/