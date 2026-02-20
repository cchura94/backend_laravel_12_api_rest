<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::get();

        return response()->json($roles, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required"
        ]);

        $role = new Role();
        $role->name = $request->name;
        $role->description = $request->description;
        $role->save();


        return response()->json(["mensaje" => "Role registrado correctamnete"], 201);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                "mensaje" => "Role no encontrado"
            ], 404);
        }

        return response()->json($role, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                "mensaje" => "Role no encontrado"
            ], 404);
        }

        $request->validate([
            "name" => "sometimes|required|string|max:255",
            "description" => "nullable|string"
        ]);

        $role->name = $request->name;
        $role->description = $request->description;
        $role->update();
 
        // $role->update($request->only(["name", "description"]));

        return response()->json([
            "mensaje" => "Role actualizado correctamente",
            "data" => $role
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                "mensaje" => "Role no encontrado"
            ], 404);
        }

        // $role->delete();

        return response()->json([
            "mensaje" => "Role eliminado correctamente"
        ], 200);
    }
}
