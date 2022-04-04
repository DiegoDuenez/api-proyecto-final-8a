<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoCode extends Model
{
    use HasFactory;

    public $table = "producto_codes";
  
    protected $fillable = [
        'requesting_user',
        'product_id',
        'creator_user',
        'code',
    ];


 
}    
