<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class NotaController extends Controller
{
    public function index(Request $request){

        $query = Nota::with(['user', 'cliente', 'movimientos']);

        // filtros
        if($request->has('tipo_nota')){
            $query->where('tipo_nota', $request->tipo_nota);
        }

        if($request->has('estado_nota')){
            $query->where('estado_nota', $request->estado_nota);
        }

        if($request->has('cliente_id')){
            $query->where('cliente_id', $request->cliente_id);
        }

        if($request->has('user_id')){
            $query->where('user_id', $request->user_id);
        }

        if($request->has(['fecha_inicio', 'fecha_fin'])){
            $query->where('fecha', [$request->fecha_inicio, $request->fecha_fin]);
        }

        // busqueda global
        if($request->has('search')){
            $query->where(function($q) use ($request){
                $q->where('observaciones', 'like', '%'.$request->search . '%')
                    ->orWhere('fecha', 'like', '%'.$request->search . '%');
            });
        }

        // paginación
        $notas = $query->orderByDesc('fecha')->paginate(15);

        return response()->json($notas);
    }

    public function store(Request $request){

        // validation
        $validated = $request->validate([
            // "fecha" => "nullable|date",
            "tipo_nota" => "required|in:venta,compra,devolucion",
            "impuestos" => "nullable|numeric",
            "estado_nota" => "nullable|string",
            "observaciones" => "nullable",
            "cliente_id" => "required|exists:clientes,id",
            // "user_id" => "nullable|exists:users,id",
            "movimientos.*.almacen_id" => "required|exists:almacens,id",
            "movimientos.*.producto_id" => "required|exists:productos,id",
            "movimientos.*.cantidad" => "required|integer|min:1",
            "movimientos.*.tipo_movimiento" => "required|in:ingreso,salida,devolucion",
            "movimientos.*.precio_unitario_compra" => "required|numeric",
            "movimientos.*.precio_unitario_venta" => "required|numeric",
            "movimientos.*.observaciones" => "nullable|string"
        ]);

        // transaction
        try {
            DB::beginTransaction();
            // Actualizar el stock de los productos

            $nota = new Nota();
            $nota->fecha = date("Y-m-d H:i:s");
            $nota->tipo_nota = $request->tipo_nota;
            $nota->impuestos = $request->impuestos;
            $nota->estado_nota = $request->estado_nota;
            $nota->observaciones = $request->observaciones;
            $nota->cliente_id = $request->cliente_id;
            $nota->user_id = Auth::user()->id;
            $nota->save();

            foreach($request->movimientos as $mov){
                $nota->movimientos()->attach($mov["almacen_id"], [
                    'producto_id' => $mov["producto_id"],
                    'cantidad' => $mov["cantidad"],
                    'tipo_movimiento' => $mov["tipo_movimiento"],
                    'precio_unitario_compra' => $mov["precio_unitario_compra"],
                    'precio_unitario_venta' => $mov["precio_unitario_venta"],
                    'observaciones' => $mov["observaciones"],
                ]);

                // actualizar stock
                $pivot = DB::table("almacen_producto")
                                ->where("almacen_id", $mov['almacen_id'])
                                ->where("producto_id", $mov['producto_id'])
                                ->first();

                if(!$pivot){
                    if($mov['tipo_movimiento'] === 'salida'){
                        throw new \Exception("No hay stock para salida en este almacen y producto");
                    }

                    DB::table("almacen_producto")->insert([
                        "almacen_id" => $mov["almacen_id"],
                        "producto_id" => $mov["producto_id"],
                        "cantidad_actual" => $mov["cantidad"]
                    ]);

                }else{
                    $nuevaCantidad = $pivot->cantidad_actual;
                

                    if($mov['tipo_movimiento'] === 'ingreso' || $mov['tipo_movimiento'] === 'devolucion'){
                        $nuevaCantidad += $mov['cantidad'];
                        
                    }elseif($mov['tipo_movimiento'] === 'salida'){
                        if($pivot->cantidad_actual < $mov['cantidad']){
                            throw new \Exception("Stock Insuficiente en salida");
                        }
                        $nuevaCantidad -= $mov['cantidad'];
                    }

                    DB::table("almacen_producto")
                        ->where('almacen_id', $mov['almacen_id'])
                        ->where('producto_id', $mov['producto_id'])
                        ->update([
                            "cantidad_actual" => $nuevaCantidad,
                        ]);
                }

            }

            // TODO BIEN
            DB::commit();
            return response()->json(["mensaje" => "Nota creada correctamente"], 201);
        
        } catch (\Exception $e) {
            // error al registrar
            DB::rollback();
            return response()->json(["mensaje" => "Error al registrar la nota", "error" => $e->getMessage()], 500);
        }
    }


    public function generarPDF(Request $request){

        $query = Nota::with(['user', 'cliente', 'movimientos']);

        // filtros
        if($request->has('tipo_nota')){
            $query->where('tipo_nota', $request->tipo_nota);
        }

        if($request->has('estado_nota')){
            $query->where('estado_nota', $request->estado_nota);
        }

        if($request->has('cliente_id')){
            $query->where('cliente_id', $request->cliente_id);
        }

        if($request->has('user_id')){
            $query->where('user_id', $request->user_id);
        }

        if($request->has(['fecha_inicio', 'fecha_fin'])){
            $query->where('fecha', [$request->fecha_inicio, $request->fecha_fin]);
        }

        // busqueda global
        if($request->has('search')){
            $query->where(function($q) use ($request){
                $q->where('observaciones', 'like', '%'.$request->search . '%')
                    ->orWhere('fecha', 'like', '%'.$request->search . '%');
            });
        }

        // paginación
        $notas = $query->orderByDesc('fecha')->get();



        $pdf = Pdf::loadView('pdf.lista_notas', ["notas" => $notas]);
        return $pdf->download('invoice.pdf');
    }
}
