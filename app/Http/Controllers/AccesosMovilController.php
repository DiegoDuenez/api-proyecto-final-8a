<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\AuthEvent;
use App\Models\AccesosMovil;
use App\Models\User;

class AccesosMovilController extends Controller
{

    public function index(){
        return response()->json(['lista'=>AccesosMovil::where('user_id',auth()->user()->id)->get()], 201);
    }

    public function aceptar(Request $request){

        $request->validate([
            'status'=>'required',
            'codigo'=>'required',
        ]);

        if($request->status == '1'){
            $am = AccesosMovil::where('codigo',$request->codigo)
            ->where('status',0)
            ->where('user_id', auth()->user()->id)
            ->where('deleted_at',null)
            ->first();
    
            $user = User::find(auth()->user()->id);
    
            if($am && $user){
                $am->status = 1;
                if($am->save()){
                    $token = $user->createToken($user->username_usuario,['user:admin'])->plainTextToken;
                    event(new AuthEvent(['status'=>$request->status, 'token'=>$token]));
                    return response()->json(['token'=>$token], 201);
                }
                else{
                    return response()->json(['mensaje'=>'error'], 400);
                }
               
            }
    
        }
        else{
            $am = AccesosMovil::where('codigo',$request->codigo)
            ->where('status',0)
            ->where('user_id', auth()->user()->id)
            ->where('deleted_at',null)
            ->first();
            $am->delete();
            event(new AuthEvent(['status'=>$request->status]));
            return response()->json(['mensaje'=>'se nego el login'], 400);
        }
       
    }
}
