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
            'username'=>'required|email',
            'password'=>'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){

            throw ValidationException::withMessages([
                'login fallido'=>['Los datos ingresados son incorrectos'],
            ]);

        }

        if($user->email_verified){

            if($user->rol_id == 1){
                $token = $user->createToken($request->email,['user:lowshow','user:lowsave','user:lowedit','user:lowdelete'])->plainTextToken;
            }
            else if($user->rol_id == 2){
                $token = $user->createToken($request->email,['user:show','user:save','user:edit','user:delete'])->plainTextToken;
            }
            else if($user->rol_id == 3){
                $token = $user->createToken($request->email,['super:user'])->plainTextToken;
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
            'username'=>'required',
            'nombre'=>'required',
            'apellidos'=>'required',
            'numero'=>'required|number|unique:users,numero_usuario',
            'email'=> 'required|email|unique:users,email_usuario',
            'password'=>'required',
        ]);

        //pendiente

        $user = new User();
        $user->username_usuario = $request->username;
        $user->nombre_usuario = $request->nombre;
        $user->apellidos_usuario = $request->apellidos;
        $user->numero_usuario = $request->numero;
        $user->email_usuario = $request->email;
        $user->password_usuario = Hash::make($request->password);
        $user->rol_id = 0;
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
