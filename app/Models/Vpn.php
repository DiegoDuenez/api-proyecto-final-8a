<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vpn extends Model
{
    use HasFactory;

    public $table = "vpn";

    protected $fillable = [
        'ip_publica',
        'ip_privada',
        'ip_vpn'
    ];
}
