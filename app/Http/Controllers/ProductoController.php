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
    public function index($id = null)
    {
        if($id){
            
            $producto = Producto::where('productos.id',$id)
            ->select('productos.*', 'users.username_usuario')
            ->join('users','users.id', 'productos.user_id')
            ->get();


            if($producto){
                return response()->json($producto, 200);
            }
            else{
                return response()->json(['mensaje'=>'no se encontro ningun producto'], 200);
            }

        }
        else{

            $productos = Producto::select('productos.*', 'users.username_usuario')
            ->join('users','users.id', 'productos.user_id')
            ->get();
            return response()->json(['productos' => $productos], 200);

        }
    }

    public function productosUsuario($id){

        $productos = Producto::where('user_id', $id)
        ->join('users','users.id', 'productos.user_id')->get();
        return response()->json(['productos' => $productos], 200);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $request->validate([
            'nombre_producto'=>'required|unique:productos,nombre_producto,NULL,id,deleted_at,NULL',
            'precio_producto'=>'required',
            'user_id' => 'required'
        ],
        [
            'nombre_producto.required' => 'El nombre del producto es requerido',
            'nombre_producto.unique' => 'El nombre del producto ya fue registrado anteriormente',
            'precio_producto.required' => 'El nombre del producto es requerido'

        ]);

        $producto = new Producto();

        $producto->nombre_producto = $request->nombre_producto;
        $producto->precio_producto = $request->precio_producto;
        $producto->user_id = $request->user_id;

        if($producto->save()){
            return response()->json(['mensaje' => 'se ha registrado el producto'], 200);
        }
        else{
            return response()->json(['mensaje' => 'no se ha registrado el producto'], 400);

        }
    }

    
    public function update(Request $request, $id)
    {

        
        if(auth()->user()->rol_id == 1){

            $producto= new Producto();

            $producto= Producto::find($id);

            $request->validate([
                'nombre_producto'=>'required|unique:productos,nombre_producto,'.$id.',id,deleted_at,NULL',
                'precio_producto'=>'required',
                'codigo_verificacion' => 'required'
             
            ],
            [
                'nombre_producto.required' => 'El nombre del producto es requerido',
                'nombre_producto.unique' => 'El nombre del producto ya fue registrado anteriormente',
                'precio_producto.required' => 'El nombre del producto es requerido'
    
            ]);

            $producto->nombre_producto = $request->nombre_producto;
            $producto->precio_producto = $request->precio_producto;

            $sp = SolicitudesPermiso::where('requesting_user', auth()->user()->id)
                ->where('requested_item', $id)
                ->where('code', $request->codigo_verificacion)
                ->where('status', 1)
                ->first();

            if($sp){
                if($producto->save()){
                    $spUp = SolicitudesPermiso::where('requesting_user', auth()->user()->id)
                    ->where('requested_item', $id)
                    ->where('code', $request->codigo_verificacion)
                    ->where('status', 1)
                    ->update(['status'=>0]);

                    return response()->json(["mensaje"=>'se ha actualizado el producto '], 201);

                }
                else{
                    return response()->json(["mensaje"=>'no se ha actualizado el producto'], 400);
                }
            }else{
                return response()->json(["mensaje"=>'accion sin autorizacion'], 400);
            }

        }
    }

    public function delete(Request $request, $id)
    {
        //if($request->user()->tokenCan('super:user')){
            if($request->user()->rol_id == 3){
                if($id){
                    $producto = Producto::find($id);
                    if($producto){
                        $producto->delete();
                        return response()->json(["mensaje"=>'se ha eliminado el producto'], 201);
                    }
                    else{
                        return response()->json(["aviso"=>'ningún producto encontrado'],200);
                    }
                }
                return response()->json(['aviso'=>'ningún parametro enviado'],400);
            }
            else{

                $request->validate([
                    'codigo_verificacion'=>'required'
                ]);

                $sp = SolicitudesPermiso::where('requesting_user', auth()->user()->id)
                    ->where('requested_item', $id)
                    ->where('code', $request->codigo_verificacion)
                    ->where('status', 1)
                    ->first();

                if($sp){
                    $producto = Producto::find($id);
                    if($producto){

                        $spUp = SolicitudesPermiso::where('requesting_user', auth()->user()->id)
                        ->where('requested_item', $id)
                        ->where('code', $request->codigo_verificacion)
                        ->where('status', 1)
                        ->update(['status'=>0]);
    
                        $producto->delete();
                        return response()->json(["mensaje"=>'se ha eliminado el producto '], 201);
                    }
                    else{
                        return response()->json(["aviso"=>'ningún producto encontrado'],200);
                    }

                }else{
                    return response()->json(["mensaje"=>'accion sin autorizacion'], 400);
                }
                //$request->user()->generateCode();
                /*$producto = Producto::find($id);
                SolicitudesPermiso::create([
                    'requesting_user' => auth()->user()->id,
                    'solicitud' => 'El usuario ' . auth()->user()->username_usuario . ' solicita poder eliminar el producto ' . $producto->nombre_producto,
                    'requested_item' => $producto->id
                ]);
                return response()->json(['aviso'=>'tu acción debe ser autorizada, ingresa el código de autorización que se mando a tu correo.'],200);
*/
            }
            
        
    }

    public function verificarExistenciaCodigo($codigo){
        if($codigo){
            $sp = SolicitudesPermiso::where('code', $codigo)
                    ->where('status', 1)
                    ->first();
            if($sp){
                return response()->json(["mensaje"=>'este codigo existe'], 200);

            }
            else{
                return response()->json(["mensaje"=>'este codigo no existe'], 400);

            }
        }
        else{
            return response()->json(["mensaje"=>'no se ha ingresado ningun codigo'], 400);
        }
    }

    public function requestPermission(Request $request){

        if(auth()->user()->rol_id == 1){
            $request->validate([
                'solicitud'=>'required',
                'requesting_user'=>'required',
                'requested_item' => 'required'
            ]);

            //$user = User::find($request->requesting_user);

            $sp = new SolicitudesPermiso();
            $sp->solicitud = $request->solicitud;
            $sp->requesting_user = $request->requesting_user;
            $sp->requested_item = $request->requested_item;
            if($sp->save()){
                return response()->json(["mensaje"=>'se ha mandado la solicitud de autorizacion'], 201);
            }
            else{
                return response()->json(["mensaje"=>'no se ha mandado la solicitud de autorizacion'], 400);
            }

        }
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
