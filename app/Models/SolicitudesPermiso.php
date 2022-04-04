<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudesPermiso extends Model
{
    use HasFactory;

    protected $fillable = [
        'requesting_user',
        'solicitud',
        'requested_item',
        'status'
    ];
}
