<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SolcitudPermisoController;
use App\Http\Controllers\AccesosMovilController;
use App\Models\AccesosMovil;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function(){
    response()->json(['mensaje'=>'jalando']);
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/rol/2', [AuthController::class, 'loginRol2']);
Route::post('/login/rol/3', [AuthController::class, 'loginRol3']);
Route::post('/esperando/auth', [AuthController::class, 'esperandoAuth']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/register/verify/{code}', [AuthController::class, 'verify']);
Route::middleware('auth:sanctum')->delete('/logout', [AuthController::class, 'logout']);
Route::post('/login/movil', [AuthController::class, 'loginMovil']);

/*
|--------------------------------------------------------------------------
| Productos Routes
|--------------------------------------------------------------------------
|
*/
Route::middleware('auth:sanctum')->get('/mostrar/productos/{id?}', [ProductoController::class, 'index']);
Route::middleware('auth:sanctum')->get('/mostrar/productos/usuarios/{id}', [ProductoController::class, 'productosUsuario']);
Route::middleware('auth:sanctum')->post('/crear/productos/', [ProductoController::class, 'create']);
Route::middleware('auth:sanctum')->post('/solicitar/permiso/producto', [ProductoController::class, 'requestPermission']);
Route::middleware('auth:sanctum')->put('/editar/productos/{id}', [ProductoController::class, 'update']);
Route::middleware('auth:sanctum')->post('/eliminar/productos/{id}', [ProductoController::class, 'delete']);
Route::middleware('auth:sanctum')->get('/verificar/codigo/{codigo}', [ProductoController::class, 'verificarExistenciaCodigo']);
//Route::middleware('auth:sanctum')->delete('/eliminar/productos/{id}/{codigo}', [ProductoController::class, 'deleteWithCode']);


/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
*/
//Route::middleware('auth:sanctum')->post('/generar-codigo/{req_user}/{producto_id}/{create_user}', [UserController::class, 'generarCodigoAutorizacion']);
Route::middleware('auth:sanctum')->get('/mostrar/usuarios/{id?}', [UserController::class, 'index']);
Route::middleware('auth:sanctum')->get('/mostrar/roles/{id?}', [UserController::class, 'getRoles']);
Route::middleware('auth:sanctum')->get('/perfil', [UserController::class, 'profile']);
Route::middleware('auth:sanctum')->put('/editar/usuarios/{id}', [UserController::class, 'update']);
Route::middleware('auth:sanctum')->put('/editar/ip/usuarios/{id}', [UserController::class, 'cambiarIp']);
Route::middleware('auth:sanctum')->post('/eliminar/usuarios/{id}', [UserController::class, 'delete']);
Route::middleware('auth:sanctum')->post('/solicitar/permiso', [UserController::class, 'requestPermission']);


/*
|--------------------------------------------------------------------------
| Solciitudes Routes
|--------------------------------------------------------------------------
|
*/
Route::middleware('auth:sanctum')->get('/mostrar/solicitudes/permisos/{id?}', [SolcitudPermisoController::class, 'index']);
Route::middleware('auth:sanctum')->post('/enviar/solictudes/rechazadas/{id}', [SolcitudPermisoController::class, 'rechazarSolicitud']);
Route::middleware('auth:sanctum')->post('/enviar/solictudes/aceptadas/{id}', [SolcitudPermisoController::class, 'aceptarSolicitud']);


/*
|--------------------------------------------------------------------------
| Accesos Moviles Route
|--------------------------------------------------------------------------
|
*/
Route::middleware('auth:sanctum')->get('/accesos', [AccesosMovilController::class, 'index']);
Route::get('/accesos/usuario/{id}', [AccesosMovilController::class, 'accesoUsuario']);
Route::middleware('auth:sanctum')->post('/aceptar/accesos', [AccesosMovilController::class, 'aceptar']);
