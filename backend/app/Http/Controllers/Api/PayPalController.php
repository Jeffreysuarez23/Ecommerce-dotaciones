<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PayPalService;
use App\Models\Orden;
use App\Models\Pago;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;

class PayPalController extends Controller
{
    protected $payPalService;

    public function __construct(PayPalService $payPalService)
    {
        $this->payPalService = $payPalService;
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'orden_id' => 'required|exists:ordenes,id'
        ]);

        $orden = Orden::find($request->orden_id);
        
        if ($orden->usuario_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Tasa de cambio aproximada COP a USD. Se debe configurar en .env
        $exchangeRate = env('EXCHANGE_RATE_COP_USD', 4000);
        $amountUSD = round($orden->total / $exchangeRate, 2);

        try {
            $paypalOrder = $this->payPalService->createOrder($amountUSD, 'USD', $orden->id);
            
            return response()->json([
                'id' => $paypalOrder['id'],
                'status' => $paypalOrder['status']
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function captureOrder(Request $request)
    {
        $request->validate([
            'paypal_order_id' => 'required|string',
            'orden_id' => 'required|exists:ordenes,id'
        ]);

        $orden = Orden::find($request->orden_id);

        try {
            DB::beginTransaction();

            $captureData = $this->payPalService->capturePayment($request->paypal_order_id);
            \Illuminate\Support\Facades\Log::info('PayPal Capture Data: ' . json_encode($captureData));

            if (isset($captureData['status']) && $captureData['status'] === 'COMPLETED') {
                $pago = Pago::where('orden_id', $orden->id)->first();
                if (!$pago) {
                    $pago = new Pago();
                    $pago->orden_id = $orden->id;
                }
                
                $pago->metodo = 'paypal';
                $pago->referencia_pasarela = $captureData['id'];
                $pago->monto = $orden->total; // Mantener monto en COP localmente
                $pago->estado = 'aprobado';
                $pago->save();

                $orden->estado = 'pagado';
                $orden->save();

                DB::commit();

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
                    'message' => 'Pago completado con éxito',
                    'pago' => $pago
                ]);
            } else {
                DB::rollBack();
                \Illuminate\Support\Facades\Log::error('El pago no fue COMPLETED: ' . json_encode($captureData));
                return response()->json(['message' => 'El pago no fue completado en PayPal'], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Excepcion en captureOrder: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function webhook(Request $request)
    {
        $headers = $request->headers->all();
        $body = $request->getContent();

        // Si se define un WEBHOOK_ID, se verifica la firma
        if (env('PAYPAL_WEBHOOK_ID')) {
            $isValid = $this->payPalService->verifyWebhookSignature($headers, $body);
            if (!$isValid) {
                Log::warning('PayPal Webhook signature verification failed');
                return response()->json(['message' => 'Firma inválida'], 400);
            }
        }

        $event = json_decode($body, true);
        Log::info('PayPal Webhook received: ' . ($event['event_type'] ?? 'unknown'));

        if (isset($event['event_type']) && ($event['event_type'] === 'CHECKOUT.ORDER.APPROVED' || $event['event_type'] === 'PAYMENT.CAPTURE.COMPLETED')) {
            $resource = $event['resource'];
            
            $referenceId = null;
            if (isset($resource['purchase_units'][0]['reference_id'])) {
                $referenceId = $resource['purchase_units'][0]['reference_id'];
            }

            if ($referenceId) {
                $orden = Orden::find($referenceId);
                if ($orden && $orden->estado !== 'pagado') {
                    
                    $pago = Pago::where('orden_id', $orden->id)->first();
                    if (!$pago) {
                        $pago = new Pago();
                        $pago->orden_id = $orden->id;
                    }

                    $pago->metodo = 'paypal';
                    $pago->transaccion_id = $resource['id'];
                    $pago->monto = $orden->total;
                    $pago->estado = 'aprobado';
                    $pago->save();

                    $orden->estado = 'pagado';
                    $orden->save();

                    Log::info("Orden {$orden->id} actualizada vía Webhook de PayPal");
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
