<?php

namespace App\Http\Controllers;

use App\Exports\ProductoExport;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $limit = isset($request->limit) ? $request->limit : 10;
        $estado = isset($request->estado) ? $request->estado : null;
        $almaceID = isset($request->almacen) ? $request->almacen : null;

        $productos = Producto::query();

        if(isset($estado)){
            $productos = $productos->where("estado", "=", $request->estado);
        }

        if(isset($request->search)){
            $search = $request->search;

            $productos = $productos->where("nombre", "Like", "%$search%")
                                    ->orWhere("marca", "Like", "%$search%");
        }

        if(isset($almaceID)){
            $productos = $productos->whereHas("almacenes", function ($query) use ($almaceID){
                $query->where("almacens.id", "=", $almaceID);
            });
        }

        $productos = $productos->with(["categoria", "almacenes"])
                                ->orderBy('id', 'desc')
                                ->paginate($limit);

        return response()->json($productos, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validar
        $request->validate([
            "nombre" => "required",
            "categoria_id" => "required|exists:categorias,id"
        ]);

        // $categoria = Categoria::find($request->categoria_id);
        // if($categoria){
            $producto = new Producto();
            $producto->nombre = $request->nombre;
            $producto->categoria_id = $request->categoria_id;
            $producto->descripcion = $request->descripcion;
            $producto->marca = $request->marca;
            $producto->precio_venta = $request->precio_venta;
            $producto->imagen = $request->imagen;
            $producto->estado = $request->estado;
            $producto->save();

        /*
        }else{
            return response()->json(["mensaje" => "Error al registrar el Producto"], 400);
        }
        */

        return response()->json(["mensaje" => "Producto Registrado"], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $producto = Producto::findOrFail($id);

        return response()->json($producto, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         // validar
         $request->validate([
            "nombre" => "required",
            "categoria_id" => "required"
        ]);

        $producto = Producto::findOrFail($id);
        $producto->nombre = $request->nombre;
        $producto->categoria_id = $request->categoria_id;
        $producto->descripcion = $request->descripcion;
        $producto->marca = $request->marca;
        $producto->precio_venta = $request->precio_venta;
        $producto->imagen = $request->imagen;
        $producto->estado = $request->estado;
        $producto->update();

        return response()->json(["mensaje" => "Producto Actualizado"], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $producto = Producto::findOrFail($id);
        $producto->estado = false;
        $producto->save();

        return response()->json($producto);

    }

    public function funActualizaImagen($id, Request $request){
        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);
        
        if($file = $request->file("imagen")){
            $direccion_url = time() . "-" .$file->getClientOriginalName();
            $file->move("imagenes", $direccion_url);


            $producto = Producto::find($id);
            $producto->imagen = "imagenes/". $direccion_url;
            $producto->update();
            return response()->json($producto);
        }
        
    }


    public function exportExcelProducto() 
    {
        return Excel::download(new ProductoExport, 'lista_productos.xlsx');
    }
}
