<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccesosMovil extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $table = "accesos_movil";

    protected $fillable = [
        'user_id',
        'codigo',
        'status',
    ];
}
