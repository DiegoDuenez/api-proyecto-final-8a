<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\UserCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

class AuthController extends Controller
{
    
    public function login(Request $request){

        $request->validate([
            'username_usuario'=>'required',
            'password_usuario'=>'required'
        ]);

        $user = User::where('username_usuario', $request->username_usuario)
        ->first();

        if(!$user || !Hash::check($request->password_usuario, $user->password_usuario)){

            throw ValidationException::withMessages([
                'login fallido'=>['Los datos ingresados son incorrectos'],
            ]);

        }

        if($user->status_usuario){
            if($user->email_verified){

                if($user->rol_id == 1){
                    $token = $user->createToken($request->username_usuario,['user:lowcreate','user:lowread','user:lowupdate','user:lowdelete'])->plainTextToken;
                    return response()->json(['token'=>$token, 'user'=>$user], 201);
                    
                }
                else if($user->rol_id == 2 || $user->rol_id == 3){
                   // $user->generateCode();

                   $code = rand(100000, 999999);
                   $usercode = new UserCode();
                   $usercode->user_Id = $user->id;
                   $usercode->code = $code;

                   if($usercode->save()){
                        $data['email_code_usuario'] = $code;
                        $data['email_usuario'] =  $user->email_usuario;
                        $data['username_usuario'] = $request->username_usuario;

                        Mail::send('emails.codigo_login_auth', $data, function($message) use ($data) {
                            $message->to($data['email_usuario'], $data['username_usuario'])->subject('Codigo de autenticación');
                        });
                   }

                    return response()->json(['mensaje'=>'se ha generado el codigo', 'user'=>$user], 201);
                
                }
                else if($user->rol_id == 3){
                    //$token = $user->createToken($request->username_usuario,['super:user'])->plainTextToken;
                    //return response()->json(['token'=>$token, 'user'=>$user], 201);

                }
    
    
            }
            else {
                throw ValidationException::withMessages([
                    'verificacion fallida'=>['La cuenta no se ha activado, verifique su correo.'],
                ]);
            }
        }
        else {
            throw ValidationException::withMessages([
                'inactiva'=>['La cuenta se ha deshabilitado.'],
            ]);
        }

    }

    public function loginRol2(Request $request){

        $request->validate([
            'username_usuario'=>'required',
            'password_usuario'=>'required',
            'codigo_autenticacion'=>'required'
        ]);

        $user = User::where('username_usuario', $request->username_usuario)
        ->first();

        if(!$user || !Hash::check($request->password_usuario, $user->password_usuario)){
            throw ValidationException::withMessages([
                'login fallido'=>['Los datos ingresados son incorrectos'],
            ]);
        }

        if($user->status_usuario){

            if($user->email_verified){

                if($user->rol_id == 2 || $user->rol_id == 3){

                    $userCode = UserCode::where('user_id', $user->id)
                    ->where('code', $request->codigo_autenticacion)
                    ->where('status',true)
                    ->where('updated_at', '>=', now()->subMinutes(5))
                    ->first();

                    if($userCode){
                        $userCode->status = false;
                        if($userCode->save()){
                            if($user->rol_id == 2){
                                $token = $user->createToken($request->username_usuario,['user:create','user:read','user:update','user:delete'])->plainTextToken;
                                return response()->json(['token'=>$token, 'user'=>$user], 201);
                                
                            }
                            else if($user->rol_id == 3){

                                if(!$this->isVpn($user->ip_public_usuario)){

                                    $code = rand(100000, 999999);
                                    $usercode = new UserCode();
                                    $usercode->user_Id = $user->id;
                                    $usercode->code = $code;
                                    if($usercode->save()){
                                        //$receiverNumber = auth()->user()->phone;
                                        $receiverNumber = "+528711223529";
                                        $message = "Tu codigo de acceso es ". $code;
                                    
                                        try {
                                            
                                            $account_sid = getenv("TWILIO_SID");
                                            $auth_token = getenv("TWILIO_TOKEN");
                                            $number = getenv("TWILIO_FROM");
                                    
                                            $client = new Client($account_sid, $auth_token);
                                            $client->messages->create($receiverNumber, [
                                                'from' => $number, 
                                                'body' => $message]);
                                            return response()->json(['mensaje'=>'se ha generado y mandado el codigo', 'user'=>$user], 201);
                                            
                                        } catch (\Exception $e) {
                                            return response()->json(['mensaje'=> $e->getMessage(), 'user'=>$user], 400);
                                        }
                                    }
                                   
                                }
                                else{

                                    $token = $user->createToken($request->username_usuario,['user:admin'])->plainTextToken;
                                    return response()->json(['token'=>$token, 'user'=>$user], 201);

                                }
                                
                            }
                        }
                    }else{
                        throw ValidationException::withMessages([
                            'codigo error'=>['El codigo no existe o ha expirado.'],
                        ]);
                    }
                
                }
                else{

                    throw ValidationException::withMessages([
                        'invalido'=>['La cuenta tiene un rol no valido para este login o no existente.'],
                    ]);

                }
    
            }
            else {

                throw ValidationException::withMessages([
                    'verificacion fallida'=>['La cuenta no se ha activado, verifique su correo.'],
                ]);

            }
        }
        else {

            throw ValidationException::withMessages([
                'inactiva'=>['La cuenta se ha deshabilitado.'],
            ]);

        }

    }

    public function isVpn($ip){
        $token = '5ea797bc6a004e5b89e5d13dd2c7e8fd';
        $url = "https://ipgeolocation.abstractapi.com/v1/?api_key=$token&ip_address=$ip";
        $details = json_decode(file_get_contents($url));
        //var_dump($details->security->is_vpn);
        if($details->security->is_vpn) { 
            return true;
        }
        else{
            return false;
        }
    }


    public function loginRol3(Request $request){

        $request->validate([
            'username_usuario'=>'required',
            'password_usuario'=>'required',
            'codigo_autenticacion'=>'required'
        ]);

        $user = User::where('username_usuario', $request->username_usuario)
        ->first();

        if(!$user || !Hash::check($request->password_usuario, $user->password_usuario)){
            throw ValidationException::withMessages([
                'login fallido'=>['Los datos ingresados son incorrectos'],
            ]);
        }

        if($user->status_usuario){

            if($user->email_verified){

                if($user->rol_id == 3){

                }
            }
            else {

                throw ValidationException::withMessages([
                    'verificacion fallida'=>['La cuenta no se ha activado, verifique su correo.'],
                ]);

            }
        }
        else {

            throw ValidationException::withMessages([
                'inactiva'=>['La cuenta se ha deshabilitado.'],
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
            'ip_public_usuario'=>'required'
        ]);

        //pendiente

        $code = rand(100000, 999999);
        $user = new User();
        $user->username_usuario = $request->username_usuario;
        $user->nombre_usuario = $request->nombre_usuario;
        $user->apellidos_usuario = $request->apellidos_usuario;
        $user->numero_usuario = $request->numero_usuario;
        $user->email_usuario = $request->email_usuario;
        $user->password_usuario = Hash::make($request->password_usuario);
        $user->rol_id = 1;
        $user->email_code_usuario = $code;
        $user->ip_public_usuario = $request->ip_public_usuario;


        if($user->save()){

            $data['email_code_usuario'] = $code;
            $data['email_usuario'] =  $request->email_usuario;
            $data['username_usuario'] = $request->username_usuario;

            Mail::send('emails.verificacion_email', $data, function($message) use ($data) {
                $message->to($data['email_usuario'], $data['username_usuario'])->subject('Por favor confirma tu correo');
            });

            return response()->json($data, 201);
            
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

    public function verify($code)
    {
        $user = User::where('email_code_usuario', $code)->
                where('email_verified', '!=', 1)
                ->first();

        if ($user){
            $user->email_verified = true;
            //$user->email_code_usuario = null;
            $user->save();

            if($user->rol_id == 1){
                $token = $user->createToken($user->username_usuario,['user:lowcreate','user:lowread','user:lowupdate','user:lowdelete'])->plainTextToken;
            }
            else if($user->rol_id == 2){
                $token = $user->createToken($user->username_usuario,['user:create','user:read','user:update','user:delete'])->plainTextToken;
            }
            else if($user->rol_id == 3){
                $token = $user->createToken($user->username_usuario,['super:user'])->plainTextToken;
            }

            return response()->json(['mensaje'=>'tu correo se ha verificado', 'token'=>$token], 201);
        }
        return abort(400, "Hubo problemas al verificar");

    }
}
