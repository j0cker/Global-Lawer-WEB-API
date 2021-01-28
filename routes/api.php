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
******************** End Points Generales ********************
*/
 
//Ingresar User
Route::get('/ingresar', 'API@Ingresar');

// Prueba SMS
Route::get('/enviarsms', 'API@SMS');
Route::get('/verifyCode', 'API@VerificarSMS');
Route::get('/sms', 'API@SMS2');
Route::get('/smsConfirm', 'API@SMSConfirm');
// Upload Docs
Route::post('/uploadDoc', 'APILawyer@UploadDoc');
//
Route::get('/getServices', 'API@GetServices');
// 
Route::get('/regEmpresa', 'API@EmpresaPost');

// Valoracion
Route::get('/valoracion', 'API@Valoracion');

// Cambiar Activacion
Route::get('/changeActivo', 'API@ChangeActivo');

// Cambiar Headhunter
Route::get('/changeHeadHunterLaw', 'API@ChangeHeadHunterLaw');

// Get Activacion
Route::get('/getActivo', 'API@GetActivo');

//Get Despachos
Route::get('/getEmpresa', 'API@GetEmpresas');

// Get Documentos
Route::get('/getDocumentos', 'API@GetDocumentos');

// Change Password
Route::get('/changePassword', 'API@ChangePassword');

//Lanzador de Correos Electrónicos
Route::get('/mailsLauncher', 'MailsLauncher@mailsLauncher');

//Lanzador de Push Notification
Route::post('/notifications', 'API@PushNotification');

//Update ID Dispositivo
Route::get('/updateIdDispositivo', 'API@UpdateIdDispositivo');

// Verificar celular
Route::get('/verificarCel', 'API@VerificarCel');

// Eliminar empresa
Route::get('/deleteEmpresa', 'API@DeleteEmpresa');

// Actualizar Empresa
Route::get('/updateEmpresa', 'API@UpdateEmpresa');

/* 
******************** End Points Normal User ********************
*/

// Registro usuarios normales
Route::get('/normal_user/registrar', 'APIUserNormal@Registrar');
// Verificación de cedula profesional
Route::get('/normal_user/verifyCedula', 'APIUserNormal@VerifyCedula');
// Get Perfil abogados
Route::get('/normal_user/getProfile', 'APIUserNormal@GetProfile');
Route::get('/normal_user/getUser', 'APIUserNormal@GetUser');

// Registrar Servicio
Route::get('/normal_user/servicePost', 'APIUserNormal@ServicePost');
Route::get('/normal_user/servicePost2', 'APIUserNormal@ServicePost2');
// Actualizar Usuario
Route::get('/normal_user/updateUser', 'APIUserNormal@UpdateUser');

// Get Servicio
Route::get('/normal_user/getService', 'APIUserNormal@GetService');

/* 
******************** End Points Abogados ********************
*/
// Registro usuarios normales
Route::get('/lawyer/registrar', 'APILawyer@Registrar');

// Show de abogados
Route::get('/lawyer/abogadosCards', 'APILawyer@AbogadosCards');

// Show Despachos
Route::get('/lawyer/despachosCards', 'APILawyer@DespachosCards');

// Get Perfil abogados
Route::get('/lawyer/getProfile', 'APILawyer@GetProfile');
Route::get('/lawyer/getLaw', 'APILawyer@GetLaw');

// Get Servicio
Route::get('/lawyer/getService', 'APILawyer@GetService');

// Get Chat Servicio
Route::get('/lawyer/getChatService', 'APILawyer@GetChatService');

// Cambiar Status Pedidos
Route::get('/lawyer/changeStatusServicio', 'API@ChangeStatusServicio');

// Actualizar Abogado
Route::get('/lawyer/updateLaw', 'APILawyer@UpdateLaw');

// Actualizar Payment
Route::get('/lawyer/updatePayment', 'APILawyer@UpdatePayment');

// Actualizar Ubicacion Abogado
Route::get('/lawyer/updateLawAddress', 'APILawyer@UpdateLawAddress');

// Actualizar Cedula Abogado
Route::get('/lawyer/updateLawCedula', 'APILawyer@UpdateLawCedula');

Route::get('/lawyer/getServiciosUsuarios', 'APILawyer@GetServiciosUsuarios');

// Cambiar Status Pedidos
Route::get('/lawyer/addServicios', 'APILawyer@AddServicios');

//Reportar Abogado
Route::get('/lawyer/reporteAbogado', 'APILawyer@ReporteAbogado');

// Stripe
Route::post('/makePayment', 'MyStripeController@chargeStripe');
