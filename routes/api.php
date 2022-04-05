<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\UserController;

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


/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/verify/{code}', [AuthController::class, 'verify']);
Route::middleware('auth:sanctum')->delete('/logout', [AuthController::class, 'logout']);


/*
|--------------------------------------------------------------------------
| Productos Routes
|--------------------------------------------------------------------------
|
*/
Route::middleware('auth:sanctum')->delete('/eliminar/productos/{id}', [ProductoController::class, 'delete']);
Route::middleware('auth:sanctum')->delete('/eliminar/productos/{id}/{codigo}', [ProductoController::class, 'deleteWithCode']);


/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
*/
Route::middleware('auth:sanctum')->post('/generar-codigo/{req_user}/{producto_id}/{create_user}', [UserController::class, 'generarCodigoAutorizacion']);
