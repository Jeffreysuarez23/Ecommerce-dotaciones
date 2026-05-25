<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Direccion;

class DireccionController extends Controller
{
    // Listar todas las direcciones
    public function index()
    {
        return response()->json(
            Direccion::all()
        );
    }

    // Crear dirección
    public function store(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'nombre_recibe' => 'required|string|max:150',
            'telefono' => 'nullable|string|max:20',
            'departamento' => 'required|string|max:80',
            'ciudad' => 'required|string|max:80',
            'direccion' => 'required|string|max:250',
            'referencia' => 'nullable|string|max:250',
            'codigo_postal' => 'nullable|string|max:10',
        ]);

        $direccion = Direccion::create([
            'usuario_id' => $request->usuario_id,
            'nombre_recibe' => $request->nombre_recibe,
            'telefono' => $request->telefono,
            'departamento' => $request->departamento,
            'ciudad' => $request->ciudad,
            'direccion' => $request->direccion,
            'referencia' => $request->referencia,
            'codigo_postal' => $request->codigo_postal,
            'es_principal' => $request->es_principal ?? 0
        ]);

        return response()->json([
            'message' => 'Dirección creada correctamente',
            'data' => $direccion
        ], 201);
    }

    // Ver una dirección
    public function show($id)
    {
        $direccion = Direccion::find($id);

        if (!$direccion) {
            return response()->json([
                'message' => 'Dirección no encontrada'
            ], 404);
        }

        return response()->json($direccion);
    }

    // Actualizar dirección
    public function update(Request $request, $id)
    {
        $direccion = Direccion::find($id);

        if (!$direccion) {
            return response()->json([
                'message' => 'Dirección no encontrada'
            ], 404);
        }

        $direccion->update($request->all());

        return response()->json([
            'message' => 'Dirección actualizada correctamente',
            'data' => $direccion
        ]);
    }

    // Eliminar dirección
    public function destroy($id)
    {
        $direccion = Direccion::find($id);

        if (!$direccion) {
            return response()->json([
                'message' => 'Dirección no encontrada'
            ], 404);
        }

        $direccion->delete();

        return response()->json([
            'message' => 'Dirección eliminada correctamente'
        ]);
    }
}