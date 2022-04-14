<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Hash;


class User extends Authenticatable implements MustVerifyEmail
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
        'email_verified',
        'status_usuario'
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

    public function setPasswordAttribute($password)
    {   
        $this->attributes['password_usuario'] = Hash::make($password);
    }

    public function generateCode($userId)
    {
        $code = rand(100000, 999999);
  
        UserCode::updateOrCreate([
            'user_id' => $userId,
            'code' => $code
        ]);
  
        /*$receiverNumber = auth()->user()->phone;
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

        } catch (\Exception $e) {
            info("Error" . $e->getMessage());
        }*/
    }
}
