<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // /api/almacen?sucursal=3
        $sucursal_id = isset($request->sucursal)?$request->sucursal: '';
        if(isset($request->sucursal)){
            // select * from almacenes where sucursal_id = 3
            $almacenes = Almacen::with(['sucursal'])->where("sucursal_id", "=", $sucursal_id)->get();
        }else{
            $almacenes = Almacen::with(['sucursal'])->get();
        }

        return response()->json($almacenes, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            "nombre" => "required",
            "codigo" => "min:3|max:10",
            "sucursal_id" => "required"
        ]);

        $alm = new Almacen();
        $alm->nombre = $request->nombre;
        $alm->codigo = $request->codigo;
        $alm->descripcion = $request->descripcion;
        $alm->sucursal_id = $request->sucursal_id;
        $alm->save();

        return response()->json(["mensaje" => "Almacen registrado correctamente"], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $alm = Almacen::find($id);

        return response()->json($alm, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "nombre" => "required",
            "codigo" => "min:3|max:10",
            "sucursal_id" => "required"
        ]);

        $alm = Almacen::find($id);
        $alm->nombre = $request->nombre;
        $alm->codigo = $request->codigo;
        $alm->descripcion = $request->descripcion;
        $alm->sucursal_id = $request->sucursal_id;
        $alm->update();

        return response()->json(["mensaje" => "Almacen actualizado correctamente"], 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
