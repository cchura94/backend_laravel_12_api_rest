<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteProveedorController extends Controller
{
    /**
     * Listar todos los clientes
     */
    public function index(Request $request)
    {
        $buscar = $request->buscar;
        if(isset($buscar)){
            $clientes = Cliente::where('razon_social', "like", "%$buscar%")->orWhere('nro_identificacion', 'like', "%$buscar%")->get();
        }else{
            $clientes = Cliente::get();
        }
        return response()->json($clientes, 200);
    }

    /**
     * Crear nuevo cliente
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string|max:255',
            'razon_social' => 'required|string|max:255',
            'nro_identificacion' => 'required|string|max:255|unique:clientes',
            'telefono' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:200',
            'estado' => 'nullable|string|max:30'
        ]);

        $cliente = Cliente::create($request->all());

        return response()->json([
            'message' => 'Cliente creado correctamente',
            'data' => $cliente
        ], 201);
    }

    /**
     * Mostrar cliente por ID
     */
    public function show(string $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        return response()->json($cliente);
    }

    /**
     * Actualizar cliente
     */
    public function update(Request $request, string $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        $request->validate([
            'tipo' => 'sometimes|required|string|max:255',
            'razon_social' => 'sometimes|required|string|max:255',
            'nro_identificacion' => 'sometimes|required|string|max:255|unique:clientes,nro_identificacion,' . $id,
            'telefono' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:200',
            'estado' => 'nullable|string|max:30'
        ]);

        $cliente->update($request->all());

        return response()->json([
            'message' => 'Cliente actualizado correctamente',
            'data' => $cliente
        ]);
    }

    /**
     * Eliminar cliente
     */
    public function destroy(string $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        $cliente->delete();

        return response()->json([
            'message' => 'Cliente eliminado correctamente'
        ]);
    }
}