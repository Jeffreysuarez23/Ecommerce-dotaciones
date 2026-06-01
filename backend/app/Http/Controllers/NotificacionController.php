<?php
namespace App\Http\Controllers;
use App\Models\Notificacion;
use Illuminate\Http\Request;

// CONTROLADOR PARA GESTIONAR NOTIFICACIONES
class NotificacionController extends Controller
{
    // OBTENER TODAS LAS NOTIFICACIONES ORDENADAS POR ID DESCENDENTE
    public function index()
    {
        return response()->json(
            Notificacion::orderBy('id', 'desc')->get()
        );
    }
    
    // CREAR UNA NUEVA NOTIFICACIÓN
    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:stock_bajo,orden,sistema,marketing',
            'titulo' => 'required|max:200',
            'mensaje' => 'required',
            'usuario_id' => 'nullable|exists:usuarios,id'
        ]);

        $notificacion = Notificacion::create([
            'usuario_id' => $request->usuario_id,
            'tipo' => $request->tipo,
            'titulo' => $request->titulo,
            'mensaje' => $request->mensaje
        ]);

        return response()->json([
            'message' => 'Notificación creada',
            'data' => $notificacion
        ], 201);
    }
    // MARCAR UNA NOTIFICACIÓN COMO LEÍDA
    public function marcarLeido($id)
{
    $notificacion = Notificacion::findOrFail($id);

    $notificacion->update([
        'leido_en' => now()
    ]);

    return response()->json([
        'message' => 'Notificación marcada como leída',
        'data' => $notificacion
    ]);
}
}