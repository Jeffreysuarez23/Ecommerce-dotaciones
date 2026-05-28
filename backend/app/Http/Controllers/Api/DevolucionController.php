<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDevolucionRequest;
use App\Models\Devolucion;
use App\Models\Orden;
use App\Models\Pago;
use Illuminate\Http\Request;

class DevolucionController extends Controller
{
    /**
     * Listar devoluciones
     */
    public function index()
    {
        return response()->json(
            Devolucion::with('orden')->get()
        );
    }

    /**
     * Ver devolución
     */
    public function show($id)
    {
        $devolucion = Devolucion::with('orden')
            ->find($id);

        if (!$devolucion) {
            return response()->json([
                'message' => 'Devolución no encontrada'
            ], 404);
        }

        return response()->json($devolucion);
    }

    /**
     * Crear devolución
     */
    public function store(StoreDevolucionRequest $request)
    {
        $orden = Orden::find($request->orden_id);

        if (!$orden) {
            return response()->json([
                'message' => 'Orden no encontrada'
            ], 404);
        }

        $devolucion = Devolucion::create([
            'orden_id' => $request->orden_id,
            'motivo' => $request->motivo,
            'estado' => 'pendiente'
        ]);

        return response()->json([
            'message' => 'Solicitud de devolución creada',
            'data' => $devolucion
        ], 201);
    }

    /**
     * Aprobar devolución
     */
    public function aprobar($id)
    {
        $devolucion = Devolucion::find($id);

        if (!$devolucion) {
            return response()->json([
                'message' => 'Devolución no encontrada'
            ], 404);
        }

        $devolucion->estado = 'aprobada';
        $devolucion->save();

        return response()->json([
            'message' => 'Devolución aprobada',
            'data' => $devolucion
        ]);
    }

    /**
     * Rechazar devolución
     */
    public function rechazar(Request $request, $id)
    {
        $devolucion = Devolucion::find($id);

        if (!$devolucion) {
            return response()->json([
                'message' => 'Devolución no encontrada'
            ], 404);
        }

        $devolucion->estado = 'rechazada';
        $devolucion->resolucion_admin = $request->resolucion_admin;
        $devolucion->save();

        return response()->json([
            'message' => 'Devolución rechazada',
            'data' => $devolucion
        ]);
    }

    /**
     * Resolver devolución
     */
    public function resolver(Request $request, $id)
    {
        $devolucion = Devolucion::find($id);

        if (!$devolucion) {
            return response()->json([
                'message' => 'Devolución no encontrada'
            ], 404);
        }

        if ($devolucion->estado !== 'aprobada') {
            return response()->json([
                'message' => 'La devolución debe estar aprobada'
            ], 400);
        }

        $devolucion->estado = 'resuelta';
        $devolucion->resolucion_admin = $request->resolucion_admin;
        $devolucion->save();

        $orden = Orden::find($devolucion->orden_id);

        if ($orden) {
            $orden->estado = 'devuelta';
            $orden->save();
        }

        Pago::where('orden_id', $devolucion->orden_id)
            ->update([
                'estado' => 'reembolsado'
            ]);

        return response()->json([
            'message' => 'Devolución resuelta y reembolso realizado'
        ]);
    }
}