<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    

    public function index(){

    }

    public function generarCodigoAutorizacionProducto($req_user, $producto_id, $create_user){

        if(auth()->user()->rol_id == 3){
            $producto = New Producto();
            $producto->generateCode($req_user, $producto_id, $create_user);
            return response()->json(["mensaje"=>'se ha generado el codigo correctamente'], 201);
        }
        else{
            return response()->json(["aviso"=>'no puedes generar un codigo de autorizacion'], 400);
        }

    }
    
    public function generarCodigoAutorizacionUsuario($req_user, $user_id, $create_user){

        if(auth()->user()->rol_id == 3){
            $producto = New Producto();
            $producto->generateCode($req_user, $user_id, $create_user);
            return response()->json(["mensaje"=>'se ha generado el codigo correctamente'], 201);
        }
        else{
            return response()->json(["aviso"=>'no puedes generar un codigo de autorizacion'], 400);
        }

    }

    public function edit(Request $request){

    }

    public function delete(){
        
    }
}
