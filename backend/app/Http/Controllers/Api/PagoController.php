<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Orden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderConfirmation;



class PagoController extends Controller
{
   // REGISTRO DE PAGO
    public function registrar(Request $request)
    {
        $request->validate([
            'orden_id' => 'required|exists:ordenes,id',
            'metodo' => 'required|string|max:50',
            'referencia_pasarela' => 'nullable|string|max:100',
            'monto' => 'required|numeric|min:1'
        ]);

        $orden = Orden::findOrFail($request->orden_id);

        $pago = Pago::create([
            'orden_id' => $orden->id,
            'metodo' => $request->metodo,
            'referencia_pasarela' => $request->referencia_pasarela,
            'estado' => 'pendiente',
            'monto' => $request->monto
        ]);

        // Enviar correo de confirmación al usuario
        try {
            $orden->load(['usuario', 'direccion', 'items.variante.producto.imagenes', 'pago']);
            if ($orden->usuario && $orden->usuario->email) {
                Mail::to($orden->usuario->email)->send(new OrderConfirmation($orden));
            }
        } catch (\Exception $mailError) {
            Log::warning('No se pudo enviar email de confirmación para orden ' . $orden->id . ': ' . $mailError->getMessage());
        }

        return response()->json([
            'message' => 'Pago registrado correctamente',
            'data' => $pago
        ], 201);
    }

   // APROBAR PAGO, ACTUALIZANDO EL ESTADO DEL PAGO A "APROBADO" Y REGISTRANDO LA FECHA DE PAGO. SI EL PAGO SE APRUEBA, SE ACTUALIZA EL ESTADO DE LA ORDEN ASOCIADA A "CONFIRMADA" PARA INDICAR QUE EL PEDIDO HA SIDO PAGADO Y ESTÁ LISTO PARA PROCESAR.
    public function aprobar($id)
    {
        $pago = Pago::findOrFail($id);

        $pago->estado = 'aprobado';
        $pago->pagado_en = now();
        $pago->save();

        // ACTUALIZAR OREDEN

        $orden = $pago->orden;

        if ($orden) {
            $orden->estado = 'confirmada';
            $orden->save();
        }

        return response()->json([
            'message' => 'Pago aprobado correctamente',
            'data' => $pago
        ]);
    }

     // RECHAZAR PAGO
    public function rechazar($id)
    {
        $pago = Pago::findOrFail($id);

        $pago->estado = 'rechazado';
        $pago->save();

        return response()->json([
            'message' => 'Pago rechazado',
            'data' => $pago
        ]);
    }

    // REMBOLSO
    public function reembolso($id)
    {
        $pago = Pago::findOrFail($id);

        $pago->estado = 'reembolsado';
        $pago->save();

       // ACTUALIZAR OREDEN

        $orden = $pago->orden;

        if ($orden) {
            $orden->estado = 'cancelada';
            $orden->save();
        }

        return response()->json([
            'message' => 'Pago reembolsado correctamente',
            'data' => $pago
        ]);
    }

    // LISTAR TODOS LOS PAGOS CON SU ORDEN ASOCIADA PARA ADMINISTRACIÓN Y SEGUIMIENTO.
    public function index()
    {
        $pagos = Pago::with('orden')->get();

        return response()->json($pagos);
    }

    // DETALLES DE UN PAGO ESPECÍFICO 
    public function show($id)
    {
        $pago = Pago::with('orden')->findOrFail($id);

        return response()->json($pago);
    }
}