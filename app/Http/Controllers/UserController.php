<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\SolicitudesPermiso;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Rol;

class UserController extends Controller
{
    

    public function index($id = null){

        if($id){
            
            $user = User::where('users.id',$id)->get();

            if($user){
                return response()->json($user, 200);
            }
            else{
                return response()->json(['mensaje'=>'no se encontro ningun usuario'], 200);
            }

        }
        else{

            $users = User::all();
            return response()->json(['users' => $users], 200);

        }

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

    public function profile(){
        return response()->json(auth()->user(), 201);
    }

    public function update(Request $request, $id){

        
        if(auth()->user()->rol_id == 1){

           

            $user = new User();

            $user = User::find($id);

            if($request->has('password_usuario')){
                $request->validate([
                    'username_usuario'=>'required',
                    'nombre_usuario'=>'required',
                    'apellidos_usuario'=>'required',
                    'numero_usuario'=>'required',
                    'email_usuario'=> 'required|email',
                    'password_usuario'=>'required',
                    'codigo_verificacion'=>'required'
                ]);
                $user->username_usuario = $request->username_usuario;
                $user->nombre_usuario = $request->nombre_usuario;
                $user->apellidos_usuario = $request->apellidos_usuario;
                $user->numero_usuario = $request->numero_usuario;
                $user->email_usuario = $request->email_usuario;
                $user->password_usuario = Hash::make($request->password_usuario);

                
            }
            else{
                $request->validate([
                    'username_usuario'=>'required',
                    'nombre_usuario'=>'required',
                    'apellidos_usuario'=>'required',
                    'numero_usuario'=>'required',
                    'email_usuario'=> 'required|email',
                    'codigo_verificacion'=>'required'

                ]);
                $user->username_usuario = $request->username_usuario;
                $user->nombre_usuario = $request->nombre_usuario;
                $user->apellidos_usuario = $request->apellidos_usuario;
                $user->numero_usuario = $request->numero_usuario;
                $user->email_usuario = $request->email_usuario;

            }

            $sp = SolicitudesPermiso::where('requesting_user', auth()->user()->id)
                ->where('requested_item', $id)
                ->where('code', $request->codigo_verificacion)
                ->where('status', 1)
                ->first();
                

            if($sp){
                if($user->save()){
                    $spUp = SolicitudesPermiso::where('requesting_user', auth()->user()->id)
                    ->where('requested_item', $id)
                    ->where('code', $request->codigo_verificacion)
                    ->where('status', 1)
                    ->update(['status'=>0]);

                    return response()->json(["mensaje"=>'se ha actualizado el usuario ', 'data' => $sp], 201);

                }
                else{
                    return response()->json(["mensaje"=>'no se ha actualizado el usuario'], 400);
                }
            }else{
                return response()->json(["mensaje"=>'accion sin autorizacion'], 400);
            }
           
        }
        
    }

    public function requestPermission(Request $request){
        if(auth()->user()->rol_id == 1){
            $request->validate([
                'solicitud'=>'required',
                'requesting_user'=>'required',
                'requested_item' => 'required'
            ]);

            //$user = User::find($request->requesting_user);

            $sp = new SolicitudesPermiso();
            $sp->solicitud = $request->solicitud;
            $sp->requesting_user = $request->requesting_user;
            $sp->requested_item = $request->requested_item;
            if($sp->save()){
                return response()->json(["mensaje"=>'se ha mandado la solicitud de autorizacion'], 201);
            }
            else{
                return response()->json(["mensaje"=>'no se ha mandado la solicitud de autorizacion'], 400);
            }

        }
    }

    public function getRoles($id = null){
        if($id){

            $rol = Rol::find($id);
            if($rol){
                return response()->json([$rol], 201);

            }
            else{
                return response()->json(["mensaje"=>'el rol no existe'], 201);
            }

        }
        else{
            return response()->json(['roles'=>Rol::all()], 201);

        }
    }

    public function delete(Request $request, $id){

        if($request->user()->rol_id == 3){
            if($id){
                $user= User::find($id);
                if($user){
                    $user->delete();
                    return response()->json(["mensaje"=>'se ha eliminado el usuario'], 201);
                }
                else{
                    return response()->json(["aviso"=>'ningún usuario encontrado'],200);
                }
            }
            return response()->json(['aviso'=>'ningún parametro enviado'],400);
        }
        else{

            $request->validate([
                'codigo_verificacion'=>'required'
            ]);

            $sp = SolicitudesPermiso::where('requesting_user', auth()->user()->id)
                ->where('requested_item', $id)
                ->where('code', $request->codigo_verificacion)
                ->where('status', 1)
                ->first();

            if($sp){
                $user= User::find($id);
                if($user){

                    $spUp = SolicitudesPermiso::where('requesting_user', auth()->user()->id)
                    ->where('requested_item', $id)
                    ->where('code', $request->codigo_verificacion)
                    ->where('status', 1)
                    ->update(['status'=>0]);

                    $user->delete();
                    return response()->json(["mensaje"=>'se ha eliminado el usuario '], 201);
                }
                else{
                    return response()->json(["aviso"=>'ningún usuario encontrado'],200);
                }

            }else{
                return response()->json(["mensaje"=>'accion sin autorizacion'], 400);
            }

        }
    }
}
