<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProductoCode;

class Producto extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $table = "productos";

    protected $fillable = [
        'nombre_producto',
        'precio_producto',
        'user_id'
    ];

    public function generateCode($requesting_user, $product_id, $creator_user)
    {
        $code = rand(100000, 999999);
  
        ProductoCode::updateOrCreate([
            'requesting_user' => $requesting_user,
            'product_id' => $product_id,
            'creator_user' => $creator_user,
            'code' => $code
        ]);
    }
}

    
