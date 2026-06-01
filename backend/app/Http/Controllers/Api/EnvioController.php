<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Envio;
use App\Models\Orden;
use Illuminate\Http\Request;

class EnvioController extends Controller
{
    
    //CREAR ENVÍO PARA UNA ORDEN EXISTENTE, GENERANDO UN NÚMERO DE GUÍA ÚNICO Y ESTABLECIENDO EL ESTADO INICIAL COMO "PREPARANDO". SI LA ORDEN YA TIENE UN ENVÍO ASOCIADO, DEVUELVE UN ERROR.


    public function store(Request $request)
    {
        $request->validate([
            'orden_id' => 'required|exists:ordenes,id',
            'transportadora' => 'required|string|max:100',
            'fecha_entrega_estimada' => 'nullable|date'
        ]);

        $orden = Orden::findOrFail($request->orden_id);

        $envioExistente = Envio::where('orden_id', $orden->id)->first();

        if ($envioExistente) {
            return response()->json([
                'message' => 'La orden ya tiene envío'
            ], 400);
        }

        $envio = Envio::create([
            'orden_id' => $orden->id,
            'transportadora' => $request->transportadora,
            'guia' => 'GUIA-' . strtoupper(uniqid()),
            'estado' => 'preparando',
            'fecha_entrega_estimada' => $request->fecha_entrega_estimada
        ]);

        return response()->json([
            'message' => 'Envío creado correctamente',
            'data' => $envio
        ], 201);
    }

   
 // LISTAR TODOS LOS ENVÍOS CON SU ORDEN ASOCIADA PARA ADMINISTRACIÓN Y SEGUIMIENTO.
    public function index()
    {
        $envios = Envio::with('orden')->get();

        return response()->json($envios);
    }

   // DETALLES DE UN ENVÍO ESPECÍFICO INCLUYENDO LA INFORMACIÓN DE LA ORDEN ASOCIADA, EL ESTADO ACTUAL Y LOS DETALLES DE LA TRANSPORTADORA.

    public function show($id)
    {
        $envio = Envio::with('orden')->findOrFail($id);

        return response()->json($envio);
    }

  // CAMBIAR EL ESTADO DE UN ENVÍO EXISTENTE, SINCRONIZANDO EL ESTADO DE LA ORDEN ASOCIADA SEGÚN LAS REGLAS DE NEGOCIO DEFINIDAS (POR EJEMPLO, SI EL ENVÍO SE MARCA COMO "ENTREGADO", LA ORDEN SE MARCA COMO "ENTREGADA"; SI SE MARCA COMO "FALLIDO", LA ORDEN SE MARCA COMO "CANCELADA").
public function cambiarEstado(Request $request, $id)
{
    $request->validate([
        'estado' => 'required|in:preparando,enviado,en_ruta,entregado,fallido'
    ]);

    $envio = Envio::findOrFail($id);

    $envio->estado = $request->estado;

    // SINCRONIZAR EL ESTADO DE LA ORDEN ASOCIADA SEGÚN LAS REGLAS DE NEGOCIO

    $orden = $envio->orden;

    switch ($request->estado) {

        case 'preparando':
            $orden->estado = 'procesando';
            break;

        case 'enviado':
            $orden->estado = 'enviado';
            break;

        case 'en_ruta':
            $orden->estado = 'enviado';
            break;

        case 'entregado':

            $envio->entregado_en = now();

            $orden->estado = 'entregado';
            break;

        case 'fallido':

            $orden->estado = 'cancelada';
            break;
    }

    $orden->save();

    $envio->save();

    return response()->json([
        'message' => 'Estado del envío actualizado',
        'data' => $envio
    ]);
}

   // TRACKING DE UN ENVÍO UTILIZANDO EL NÚMERO DE GUÍA, MOSTRANDO EL ESTADO ACTUAL DEL ENVÍO, LA ORDEN ASOCIADA Y LOS DETALLES DE LA TRANSPORTADORA PARA QUE LOS CLIENTES PUEDAN SEGUIR SU PEDIDO EN TIEMPO REAL.

    public function tracking($guia)
    {
        $envio = Envio::with('orden')
            ->where('guia', $guia)
            ->firstOrFail();

        return response()->json([
            'tracking' => $envio
        ]);
    }
}