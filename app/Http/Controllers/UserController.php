<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // listar
        $usuarios = DB::table("users")
                        ->select(['id', 'name', 'email', 'created_at'])
                        ->paginate(10);
        return response()->json($usuarios, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required"
        ]);
        // store
        DB::table('users')->insert([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password),
        ]);
        return response()->json(["mensaje" => "Usuario registrado"]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // mostrar
        $user = DB::table('users')->find($id);

        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // modificar
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users,email,".$id
        ]);

        $data = $request->all();
        $data["password"] = bcrypt($request->password);

        DB::table('users')->where('id', '=', $id)->update($data);

        return response()->json(["mensaje" => "Usuario actualizado"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // eliminar
        $user = DB::table('users')->find($id);
        
        return response()->json(["mensaje" => "Usuario Eliminado"]);
    }
}
