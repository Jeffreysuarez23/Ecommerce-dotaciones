<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\LonaTalla;

// CONTROLADOR DE TALLAS DE LONA 
class LonaTallaController extends Controller
{
    // LISTAR
    public function index()
    {
        return response()->json(
            LonaTalla::orderBy('id', 'desc')->get()
        );
    }

    // CREAR
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lona_id' => 'required|exists:lonas,id',
            'talla' => 'required|string|max:10',
            'cantidad' => 'required|integer|min:0'
        ]);

        $talla = LonaTalla::create($validated);

        return response()->json([
            'message' => 'Talla creada correctamente',
            'data' => $talla
        ], 201);
    }

    // ACTUALIZAR
    public function update(Request $request, $id)
    {
        $talla = LonaTalla::findOrFail($id);

        $validated = $request->validate([
            'cantidad' => 'required|integer|min:0'
        ]);

        $talla->update($validated);

        return response()->json([
            'message' => 'Talla actualizada',
            'data' => $talla
        ]);
    }

    // ELIMINAR
    public function destroy($id)
    {
        $talla = LonaTalla::findOrFail($id);
        $talla->delete();

        return response()->json([
            'message' => 'Talla eliminada'
        ]);
    }
}