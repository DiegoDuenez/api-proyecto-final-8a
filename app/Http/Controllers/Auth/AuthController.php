<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    
    public function login(Request $request){

        $request->validate([
            'username_usuario'=>'required',
            'password_usuario'=>'required'
        ]);

        $user = User::where('username_usuario', $request->username_usuario)->first();

        if(!$user || !Hash::check($request->password_usuario, $user->password_usuario)){

            throw ValidationException::withMessages([
                'login fallido'=>['Los datos ingresados son incorrectos'],
            ]);

        }

        if($user->email_verified){

            if($user->rol_id == 1){
                $token = $user->createToken($request->username_usuario,['user:lowcreate','user:lowread','user:lowupdate','user:lowdelete'])->plainTextToken;
            }
            else if($user->rol_id == 2){
                $token = $user->createToken($request->username_usuario,['user:create','user:read','user:update','user:delete'])->plainTextToken;
            }
            else if($user->rol_id == 3){
                $token = $user->createToken($request->username_usuario,['super:user'])->plainTextToken;
            }

            return response()->json(['token'=>$token], 201);

        }
        else {
            throw ValidationException::withMessages([
                'verificacion fallida'=>['La cuenta no se ha activado, verifique su correo.'],
            ]);
        }

    }

    public function register(Request $request){

        $request ->validate([
            'username_usuario'=>'required',
            'nombre_usuario'=>'required',
            'apellidos_usuario'=>'required',
            'numero_usuario'=>'required|unique:users,numero_usuario',
            'email_usuario'=> 'required|email|unique:users,email_usuario',
            'password_usuario'=>'required',
        ]);

        //pendiente

        $user = new User();
        $user->username_usuario = $request->username_usuario;
        $user->nombre_usuario = $request->nombre_usuario;
        $user->apellidos_usuario = $request->apellidos_usuario;
        $user->numero_usuario = $request->numero_usuario;
        $user->email_usuario = $request->email_usuario;
        $user->password_usuario = Hash::make($request->password_usuario);
        $user->rol_id = 1;
        $user->email_code_usuario = '12345';

        if($user->save()){

            return response()->json($user, 201);
            //$user = Auth::user();
            /*$correo = Mail::to($request->user()->email)->send(new Activacion($user));
            return response()->json($user, 201);*/
            /*Mail::send('emails.activacioncorreo', $datos, function ($mail) use ($datos){
                $mail->to($datos['email'], $datos['name'])->subject('Activa tu cuenta para poder logearte')->from('19170154@uttcampus.edu.mx');

            });
            return response()->json($user, 201);*/
        }
        return abort(400, "Hubo problemas al registrarse");
        //$datos['name'] = $user->name = $request->name;
       // $datos['email'] = $user->email = $request->email;
        
        //$datos['codigo'] = $user->codigo_act = Str::random(10);;

    }

    public function logout(Request $request){

        return response()->json(["bye"=>$request->user()->tokens()->delete()],200);

    }
}
