<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ProductoCode;
use App\Models\SolicitudesPermiso;
use App\Models\UserCode;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function show(Producto $producto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function edit(Producto $producto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Producto $producto)
    {
        //
    }

    public function delete(Request $request, $id)
    {
        //if($request->user()->tokenCan('super:user')){
            if($request->user()->rol_id == 3){
                if($id){
                    $producto = Producto::find($id);
                    if($producto){
                        $producto->delete();
                        return response()->json(['productos'=>Producto::all()],200);
                    }
                    else{
                        return response()->json(["aviso"=>'ningún producto encontrado'],200);
                    }
                }
                return response()->json(['aviso'=>'ningún parametro enviado'],400);
            }
            else{
                //$request->user()->generateCode();
                $producto = Producto::find($id);
                SolicitudesPermiso::create([
                    'requesting_user' => auth()->user()->id,
                    'solicitud' => 'El usuario ' . auth()->user()->username_usuario . ' solicita poder eliminar el producto ' . $producto->nombre_producto,
                    'requested_item' => $producto->id
                ]);
                return response()->json(['aviso'=>'tu acción debe ser autorizada, ingresa el código de autorización que se mando a tu correo.'],200);

            }
            
        /*}
        else{
            return response()->json(['aviso'=>'permisos invalidos'],400);
        }*/
    }

    public function deleteWithCode(Request $request, $id, $codigo){

        if($request->user()->rol_id != 3){

            if($id && $codigo){

                $producto = Producto::find($id);
               
                if($producto){
                    $codigo = ProductoCode::where('requesting_user', auth()->user()->id)
                    ->where('product_id', $id)
                    ->where('code', $codigo)
                    ->where('status', 1)
                    ->exists();
                    if($codigo){
                        $producto->delete();
                        return response()->json(['productos'=>Producto::all()],200);
                    }
                    else{
                        return response()->json(['aviso'=>'el código ingresado no es valido'],200);

                    }
                }
                else{
                    return response()->json(["aviso"=>'ningún producto encontrado'],200);

                }
            }
            
    
        }
        
    }
}
