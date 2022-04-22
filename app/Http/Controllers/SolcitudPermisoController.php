<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SolicitudesPermiso;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Str;

class SolcitudPermisoController extends Controller
{
    

    public function index($id = null){

        if($id){

            $sp = SolicitudesPermiso::select('solicitudes_permisos.*', 'users.username_usuario', 'productos.nombre_producto')
            ->join('users', 'users.id', 'solicitudes_permisos.requesting_user')
            ->leftJoin('productos', 'productos.id', 'solicitudes_permisos.requested_item')
            ->where('solicitudes_permisos.id',$id)
            ->get();

            if($sp){
                return response()->json(['solicitud'=>$sp], 200);
            }
            else{
                return response()->json(['mensaje'=>'no se encontro la solicitud'], 400);
            }
        }
        else{

            $sp = DB::select("select solicitudes_permisos.*, ru.username_usuario as 'ReqUser', IF(tipo = 'usuario', ri.username_usuario , productos.nombre_producto) as 'ReqItem' 
            from solicitudes_permisos
            left join users as ru on ru.id = solicitudes_permisos.requesting_user
            left join productos on productos.id = solicitudes_permisos.requested_item
            left join users as ri on ri.id = solicitudes_permisos.requested_item 
            order by solicitudes_permisos.id desc
             ;");

            return response()->json(['solicitudes'=>$sp], 200);
        }


    }

    public function aceptarSolicitud(Request $request, $id){

        $sp = SolicitudesPermiso::find($id);
        if($sp){
            $request->validate([
                'codigo'=>'required',
            ]);
            $sp->code = $request->codigo;
            if($sp->save()){

                $user = User::find($sp->requesting_user);

                $slice = Str::after($sp->solicitud, 'poder');

                $data['email_usuario'] =  $user->email_usuario;
                $data['username_usuario'] = $user->username_usuario;
                $data['codigo'] = $request->codigo;
                $data['accion'] = $slice;

        
                Mail::send('emails.peticion_aceptada', $data, function($message) use ($data) {
                    $message->to($data['email_usuario'], $data['username_usuario'])->subject('Petición aceptada');
                });
                return response()->json(['mensajes'=>'se ha generado un codigo para la solicitud'], 200);
            }else{
                return response()->json(['mensajes'=>'hubo problemas'], 400);
            }

        }else{
            return response()->json(['mensajes'=>'no se encontro la solicitud'], 200);
        }

    }

    public function rechazarSolicitud(Request $request, $id){

        $request->validate([
            'mensaje'=>'required'
        ]);

        $sp = SolicitudesPermiso::find($id);
        if($sp){
            $sp->status = 2;

            if($sp->save()){
                $user = User::find($sp->requesting_user);
                $slice = Str::after($sp->solicitud, 'poder');

                if($user){
                    $data['email_usuario'] =  $user->email_usuario;
                    $data['username_usuario'] = $user->username_usuario;
                    $data['cuerpo'] = $request->mensaje;
                    $data['accion'] = $slice;
            
                    Mail::send('emails.peticion_rechazada', $data, function($message) use ($data) {
                        $message->to($data['email_usuario'], $data['username_usuario'])->subject('Petición rechazada');
                    });
            
                    return response()->json(['mensajes'=>'email enviado de peticion rechazada'], 200);
                }
                else{
                    return response()->json(['mensajes'=>'no se encontro al usuario solicitado'], 400);
                }
                
            }
            else{
                return response()->json(['mensajes'=>'error no se actualizo la peticion'], 400);
            }
           
        }
        else{
            return response()->json(['mensajes'=>'no se encontro la peticion'], 400);
        }
       
    }

}
