<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\User;
use App\Models\Vpn;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        Rol::create([ 
            'nombre_rol'=>'bajo nivel'
        ]);
        Rol::create([ 
            'nombre_rol'=>'usuario normal'
        ]);
        Rol::create([ 
            'nombre_rol'=>'administrador'
        ]);

        User::create([
            'username_usuario' => 'admin',
            'nombre_usuario' => 'Diego',
            'apellidos_usuario' => 'Dueñez',
            'email_usuario' => 'diego@gmail.com',
            'password_usuario' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'numero_usuario' => '+528711223529',
            'email_verified'=>'1',
            'rol_id' => '3',
            'ip_public_usuario' => '189.145.97.176'
        ]);

        Vpn::create([
            'ip_publica'=>'164.92.121.241',
            'ip_privada'=>'10.124.0.6',
            //'ip_vpn'=> '192.168.127.233'
            'ip_vpn'=> '192.168.0.1'
        ]);
    }
}
