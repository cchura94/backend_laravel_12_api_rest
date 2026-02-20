<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::with('roles')->get();

        return response()->json($permissions, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'action' => 'required|string|max:50',
            'subject' => 'required|string|max:50',
            'visibleInMenu' => 'boolean',
            'label' => 'nullable|string|max:255',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $permission = Permission::create([
            'action' => $request->action,
            'subject' => $request->subject,
            'visibleInMenu' => $request->visibleInMenu ?? true,
            'label' => $request->label
        ]);

        // Relación muchos a muchos
        if ($request->has('roles')) {
            $permission->roles()->sync($request->roles);
        }

        return response()->json([
            "mensaje" => "Permiso creado correctamente",
            "data" => $permission->load('roles')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $permission = Permission::with('roles')->find($id);

        if (!$permission) {
            return response()->json([
                "mensaje" => "Permiso no encontrado"
            ], 404);
        }

        return response()->json($permission, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json([
                "mensaje" => "Permiso no encontrado"
            ], 404);
        }

        $request->validate([
            'action' => 'sometimes|required|string|max:50',
            'subject' => 'sometimes|required|string|max:50',
            'visibleInMenu' => 'boolean',
            'label' => 'nullable|string|max:255',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $permission->update($request->only([
            'action',
            'subject',
            'visibleInMenu',
            'label'
        ]));

        if ($request->has('roles')) {
            $permission->roles()->sync($request->roles);
        }

        return response()->json([
            "mensaje" => "Permiso actualizado correctamente",
            "data" => $permission->load('roles')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json([
                "mensaje" => "Permiso no encontrado"
            ], 404);
        }

        $permission->roles()->detach();
        $permission->delete();

        return response()->json([
            "mensaje" => "Permiso eliminado correctamente"
        ], 200);
    }
}