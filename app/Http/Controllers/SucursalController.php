<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sucursales = Sucursal::get();
        return response()->json($sucursales);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validar
        $request->validate([
            "nombre" => "required",
            "direccion" => "string",
            "ciudad" => "string"
        ]);

        $sucursal = new Sucursal();
        $sucursal->nombre = $request->nombre;
        $sucursal->direccion = $request->direccion;
        $sucursal->ciudad = $request->ciudad;

        $sucursal->save();

        return response()->json($sucursal);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sucursal = Sucursal::find();
        return response()->json($sucursal);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         // validar
         $request->validate([
            "nombre" => "required",
            "direccion" => "string",
            "ciudad" => "string"
        ]);

        $sucursal = Sucursal::find();
        $sucursal->nombre = $request->nombre;
        $sucursal->direccion = $request->direccion;
        $sucursal->ciudad = $request->ciudad;

        $sucursal->save();

        return response()->json($sucursal);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
