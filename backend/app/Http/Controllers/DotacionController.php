<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Dotacion;

// CONTROLADOR PARA GESTIONAR LAS DOTACIONES, QUE SON GRUPOS DE PRODUCTOS (LONAS) ASOCIADOS A UN TIPO DE USO. PERMITE CREAR, LISTAR, ACTUALIZAR Y ELIMINAR LAS DOTACIONES.
class DotacionController extends Controller
{
    //  LISTAR
    public function index()
    {
        $dotaciones = Dotacion::orderBy('id', 'desc')->get();

        return response()->json($dotaciones);
    }

    //  CREAR
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:120',
            'descripcion' => 'nullable|string',
            'min_lonas' => 'nullable|integer|min:0',
            'max_lonas' => 'nullable|integer|min:1'
        ]);

        $validated['min_lonas'] = $validated['min_lonas'] ?? 3;
        $validated['max_lonas'] = $validated['max_lonas'] ?? 10;
        $validated['lonas_activas'] = 0;

        $dotacion = Dotacion::create($validated);

        return response()->json([
            'message' => 'Dotación creada correctamente',
            'data' => $dotacion
        ], 201);
    }

    //  VER UNA
    public function show($id)
    {
        $dotacion = Dotacion::with('lonas')->findOrFail($id);

        return response()->json($dotacion);
    }

    //  ACTUALIZAR
    public function update(Request $request, $id)
    {
        $dotacion = Dotacion::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:120',
            'descripcion' => 'nullable|string',
            'min_lonas' => 'nullable|integer|min:0',
            'max_lonas' => 'nullable|integer|min:1'
        ]);

        $dotacion->update($validated);

        return response()->json([
            'message' => 'Dotación actualizada',
            'data' => $dotacion
        ]);
    }

    //  ELIMINAR
    public function destroy($id)
    {
        $dotacion = Dotacion::findOrFail($id);

        //  No borrar si tiene lonas
        if ($dotacion->lonas()->count() > 0) {
            return response()->json([
                'message' => 'No puedes eliminar una dotación con lonas'
            ], 400);
        }

        $dotacion->delete();

        return response()->json([
            'message' => 'Dotación eliminada'
        ]);
    }
}