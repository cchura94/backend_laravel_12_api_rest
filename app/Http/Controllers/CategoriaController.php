<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // SQL
        $usuarios = DB::select("SELECT * FROM categorias");
        return response()->json($usuarios);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validar
        $request->validate([
            "nombre" => "required|min:3|max:30"
        ]);

        DB::insert("INSERT INTO categorias (nombre, descripcion) values (?, ?)", [
            $request->nombre,
            $request->descripcion
        ]);

        return response()->json(["mensaje" => "Categoria registrada"], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $usuario = DB::select("SELECT * FROM categorias where id = ?", [$id]);
        return response()->json($usuario);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $affected = DB::update(
            "UPDATE categorias SET nombre = ?, descripcion = ? WHERE id = ?",
            [
                $request->nombre,
                $request->descripcion,
                $id
            ]
        );

        if ($affected === 0) {
            return response()->json([
                'message' => 'Categoría no encontrada o sin cambios'
            ], 404);
        }

        return response()->json([
            'message' => 'Categoría actualizada correctamente'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::delete("DELETE FROM categorias where id=?", [$id]);

        return response()->json(["mensaje" => "Categoria Eliminada"]);
    }
}
