<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Twilio\Rest\Client;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username_usuario',
        'nombre_usuario',
        'apellidos_usuario',
        'email_usuario',
        'email_code_usuario',
        'password_usuario',
        'numero_usuario',
        'rol_id',
        'email_verified'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_usuario',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function generateCode()
    {
        $code = rand(100000, 999999);
  
        UserCode::updateOrCreate([
            'user_id' => auth()->user()->id,
            'code' => $code
        ]);
  
        $receiverNumber = auth()->user()->phone;
        //$receiverNumber = "+528711223529";
        $message = "Tu codigo de acceso es ". $code;
    
        try {
            
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $number = getenv("TWILIO_FROM");
    
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber, [
                'from' => $number, 
                'body' => $message]);

            /*$filename = "nombreFile.txt";
            Storage::disk('spaces')->putFileAs('/files/', "ejemplo" ,$filename,'public');*/
           /*$user = Auth::user();
            $user->avatar = $filename;
            $user->save();    */

            /*Storage::disk('spaces')->put($code.'.txt','tu codigo de acceso es: '.$code,'public');
            $file = Storage::disk('spaces')->url($code.'.txt');
    
            $fileurlcdn = str_replace("digitaloceanspaces","cdn.digitaloceanspaces",$file);
            //$url = Storage::url($code.'.txt');
            error_log('Some message here.');
            //error_log($file);
            //error_log($fileurlcdn);
            $details = [
                'title' => 'Mail enviado por el Escenario #2',
                'code' => $code,
                'url' => $file
            ];*/
                    
    
        } catch (\Exception $e) {
            info("Error" . $e->getMessage());
        }
    }
}
