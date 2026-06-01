<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\HistorialLona;

// CONTROLADOR DE HISTORIAL DE LONAS    
class HistorialLonaController extends Controller
{
    // LISTAR TODO
    public function index()
    {
        $historial = HistorialLona::with('lona')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($historial);
    }

    // REGISTRAR MANUAL
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lona_id' => 'required|exists:lonas,id',
            'accion' => 'required|in:descuento,ajuste_manual,ingreso,agotado',
            'talla' => 'nullable|string|max:10',
            'cantidad_cambio' => 'nullable|integer',
            'cantidad_restante' => 'nullable|integer',
            'notas' => 'nullable|string'
        ]);

        $historial = HistorialLona::create($validated);

        return response()->json([
            'message' => 'Movimiento registrado correctamente',
            'data' => $historial
        ], 201);
    }

    // FILTRAR POR LONA
    public function show($lona_id)
    {
        $historial = HistorialLona::where('lona_id', $lona_id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($historial);
    }
}