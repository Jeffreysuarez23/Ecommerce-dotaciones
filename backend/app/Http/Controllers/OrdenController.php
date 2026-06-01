<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Carrito;
use App\Models\Orden;
use App\Models\OrdenItem;
use App\Models\Cupon;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrdenController extends Controller
{
    // CREAR ORDEN DESDE CARRITO (con cupones, envío y notas)
    public function crearDesdeCarrito(Request $request)
    {
        $request->validate([
            'carrito_id' => 'required|exists:carritos,id',
            'direccion_id' => 'required|exists:direcciones,id',
            'notas_cliente' => 'nullable|string|max:500',
        ]);

        $usuario = $request->user();

        DB::beginTransaction();

        try {

            $carrito = Carrito::with('items.variante.producto')
                ->findOrFail($request->carrito_id);

            if ($carrito->items->count() == 0) {
                return response()->json([
                    'message' => 'El carrito está vacío'
                ], 400);
            }

            $subtotal = 0;

            // Validar stock antes de crear la orden
            foreach ($carrito->items as $item) {
                $variante = $item->variante;
                $base = $variante->producto->precio_minorista + $variante->precio_extra;
                $descuentoPorcentaje = intval($variante->descuento) ?? 0;
                $precioFinal = $base * (1 - $descuentoPorcentaje / 100);

                $subtotal += ($precioFinal * $item->cantidad);

                // Verificar stock en lona_tallas si la variante tiene lona
                if ($variante->lona_id) {
                    $lonaTalla = DB::table('lona_tallas')
                        ->where('lona_id', $variante->lona_id)
                        ->where('talla', $variante->talla)
                        ->first();

                    if (!$lonaTalla) {
                        return response()->json([
                            'message' => "No hay stock registrado para {$variante->producto->nombre} (Talla: {$variante->talla}, Color: {$variante->color})"
                        ], 422);
                    }

                    if ($lonaTalla->cantidad < $item->cantidad) {
                        $disponible = $lonaTalla->cantidad;
                        return response()->json([
                            'message' => "Stock insuficiente para {$variante->producto->nombre} (Talla: {$variante->talla}, Color: {$variante->color}). Disponible: {$disponible}, Solicitado: {$item->cantidad}"
                        ], 422);
                    }
                }
            }

            // Aplicar cupón si existe
            $descuento = 0;
            $cupon_id = $carrito->cupon_id;

            if ($cupon_id) {
                $cupon = Cupon::find($cupon_id);
                if ($cupon && $cupon->activo) {
                    if ($cupon->tipo === 'porcentaje') {
                        $descuento = ($subtotal * $cupon->valor) / 100;
                    } else {
                        $descuento = $cupon->valor;
                    }
                    // Incrementar usos
                    $cupon->usos_actuales = ($cupon->usos_actuales ?? 0) + 1;
                    $cupon->save();
                }
            }

            // Costo de envío
            $envio_costo = $subtotal > 200000 ? 0 : 15000;

            $total = $subtotal - $descuento + $envio_costo;

            $orden = Orden::create([
                'usuario_id' => $usuario->id,
                'direccion_id' => $request->direccion_id,
                'cupon_id' => $cupon_id,
                'numero' => 'ORD-' . time(),
                'estado' => 'pendiente',
                'tipo_precio' => 'minorista',
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'envio_costo' => $envio_costo,
                'total' => $total,
                'notas_cliente' => $request->notas_cliente
            ]);

            foreach ($carrito->items as $item) {
                $base = $item->variante->producto->precio_minorista + $item->variante->precio_extra;
                $descuentoPorcentaje = intval($item->variante->descuento) ?? 0;
                $precioFinal = $base * (1 - $descuentoPorcentaje / 100);

                OrdenItem::create([
                    'orden_id' => $orden->id,
                    'variante_id' => $item->variante_id,
                    'lona_id_snapshot' => $item->lona_id,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $precioFinal,
                    'total_linea' => $precioFinal * $item->cantidad
                ]);
            }

            // Vaciar carrito
            $carrito->items()->delete();
            $carrito->cupon_id = null;
            $carrito->save();

            // Notificación para admin
            Notificacion::create([
                'usuario_id' => null,
                'tipo' => 'orden',
                'titulo' => 'Nueva orden recibida',
                'mensaje' => 'Se ha creado la orden ' . $orden->numero . ' por $' . number_format($total, 0) . ' COP'
            ]);

            DB::commit();

            // Verificar el stock restante para notificar si se agotó
            foreach ($carrito->items as $item) {
                $variante = $item->variante;
                if ($variante && $variante->lona_id) {
                    $stockRestante = DB::table('lona_tallas')
                        ->where('lona_id', $variante->lona_id)
                        ->where('talla', $variante->talla)
                        ->value('cantidad');

                    if ($stockRestante !== null && $stockRestante <= 0) {
                        Notificacion::create([
                            'usuario_id' => null,
                            'tipo' => 'stock_bajo',
                            'titulo' => 'Producto Agotado',
                            'mensaje' => "El producto {$variante->producto->nombre} (Talla: {$variante->talla}, Color: {$variante->color}) se ha quedado sin stock (0 unidades)."
                        ]);
                    } elseif ($stockRestante !== null && $stockRestante <= 5) {
                        Notificacion::create([
                            'usuario_id' => null,
                            'tipo' => 'stock_bajo',
                            'titulo' => 'Stock Crítico',
                            'mensaje' => "El producto {$variante->producto->nombre} (Talla: {$variante->talla}, Color: {$variante->color}) tiene stock bajo ({$stockRestante} unidades)."
                        ]);
                    }
                }
            }

            // Cargar relaciones para la respuesta
            $orden->load(['items.variante.producto.imagenes', 'direccion', 'usuario']);

            return response()->json([
                'message' => 'Orden creada correctamente',
                'data' => $orden
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            \Illuminate\Support\Facades\Log::error('Error al crear orden: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            // Detectar errores de stock del trigger MySQL
            $msg = $e->getMessage();
            if (str_contains($msg, 'Stock insuficiente') || str_contains($msg, 'stock para esa talla') || str_contains($msg, 'sin lona asociada')) {
                return response()->json([
                    'message' => 'Stock insuficiente para uno o más productos. Por favor, revisa tu carrito.'
                ], 422);
            }

            return response()->json([
                'message' => 'Error al crear orden',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // MIS PEDIDOS (del usuario autenticado)
    public function misPedidos(Request $request)
    {
        $usuario = $request->user();

        $ordenes = Orden::with([
            'items.variante.producto.imagenes',
            'direccion',
            'pago',
            'envio'
        ])
        ->where('usuario_id', $usuario->id)
        ->orderBy('creado_en', 'desc')
        ->get();

        return response()->json([
            'message' => 'Mis pedidos',
            'data' => $ordenes
        ]);
    }

    // LISTAR ORDENES (admin)
    public function index()
    {
        $ordenes = Orden::with([
            'items.variante.producto.imagenes',
            'direccion',
            'usuario',
            'envio'
        ])->get();

        return response()->json([
            'message' => 'Listado de órdenes',
            'data' => $ordenes
        ]);
    }

    // VER DETALLE ORDEN
    public function show(Request $request, $id)
    {
        $orden = Orden::with([
            'items.variante.producto.imagenes',
            'direccion',
            'usuario',
            'envio',
            'pago'
        ])->find($id);

        if (!$orden) {
            return response()->json([
                'message' => 'Orden no encontrada'
            ], 404);
        }

        // Si el usuario no es admin, solo puede ver sus propias órdenes
        $user = $request->user();
        if ($user && $user->rol === 'cliente' && $orden->usuario_id !== $user->id) {
            return response()->json([
                'message' => 'No autorizado'
            ], 403);
        }

        return response()->json([
            'message' => 'Detalle de orden',
            'data' => $orden
        ]);
    }

    // CAMBIAR ESTADO
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
           'estado' => 'required|in:pendiente,pagado,confirmada,procesando,enviado,entregado,cancelada,devuelta'
        ]);

        $orden = Orden::find($id);

        if (!$orden) {
            return response()->json([
                'message' => 'Orden no encontrada'
            ], 404);
        }

        $orden->estado = $request->estado;
        $orden->save();

        return response()->json([
            'message' => 'Estado actualizado correctamente',
            'data' => $orden
        ]);
    }

    // CANCELAR ORDEN
    public function cancelar($id)
    {
        $orden = Orden::find($id);

        if (!$orden) {
            return response()->json([
                'message' => 'Orden no encontrada'
            ], 404);
        }

        $orden->estado = 'cancelada';
        $orden->save();

        return response()->json([
            'message' => 'Orden cancelada correctamente',
            'data' => $orden
        ]);
    }

    // ENVIAR CORREO DE CONFIRMACIÓN DE COMPRA
    public static function enviarCorreoConfirmacion(Orden $orden)
    {
        $orden->load(['items.variante.producto.imagenes', 'direccion', 'usuario', 'pago']);

        $usuario = $orden->usuario;
        if (!$usuario || !$usuario->email) return;

        $itemsHtml = '';
        foreach ($orden->items as $item) {
            $nombre = $item->variante->producto->nombre ?? 'Producto';
            $variante = ($item->variante->color ?? '') . ' - ' . ($item->variante->talla ?? '');
            $itemsHtml .= '
            <tr>
                <td style="padding:12px 16px;border-bottom:1px solid #f0ebe3;font-size:14px;color:#333;">' . htmlspecialchars($nombre) . '<br><span style="font-size:12px;color:#999;">' . htmlspecialchars($variante) . '</span></td>
                <td style="padding:12px 16px;border-bottom:1px solid #f0ebe3;font-size:14px;color:#333;text-align:center;">' . $item->cantidad . '</td>
                <td style="padding:12px 16px;border-bottom:1px solid #f0ebe3;font-size:14px;color:#333;text-align:right;">$' . number_format($item->precio_unitario, 0) . '</td>
                <td style="padding:12px 16px;border-bottom:1px solid #f0ebe3;font-size:14px;color:#6b5f4e;text-align:right;font-weight:600;">$' . number_format($item->total_linea, 0) . '</td>
            </tr>';
        }

        $pago = $orden->pago->first();
        $metodoPago = $pago ? $pago->metodo : 'Pendiente';
        $estadoPago = $pago ? $pago->estado : 'pendiente';

        $direccion = $orden->direccion;
        $direccionTexto = $direccion
            ? htmlspecialchars(($direccion->nombre_recibe ?? '') . ' | ' . $direccion->direccion . ', ' . $direccion->ciudad . ', ' . $direccion->departamento)
            : 'No especificada';

        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5174');

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 40px 0; }
                .card { max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; padding: 48px 40px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
                .logo { text-align: center; font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px; }
                .subtitle { text-align: center; font-size: 14px; color: #999; margin-bottom: 32px; }
                h2 { font-size: 22px; color: #1a1a1a; margin: 0 0 8px 0; }
                .order-number { display: inline-block; background: #f5f0e8; color: #6b5f4e; padding: 8px 20px; border-radius: 100px; font-size: 14px; font-weight: 600; margin-bottom: 24px; }
                .section-label { font-size: 10px; font-weight: 600; letter-spacing: 0.12em; color: #aaa; text-transform: uppercase; margin: 24px 0 8px 0; }
                .info-text { font-size: 14px; color: #555; line-height: 1.6; margin: 0 0 4px 0; }
                table { width: 100%; border-collapse: collapse; margin: 16px 0; }
                th { background: #1a1a1a; color: white; padding: 12px 16px; font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; text-align: left; }
                th:nth-child(2), th:nth-child(3), th:nth-child(4) { text-align: center; }
                th:last-child { text-align: right; }
                .total-row { font-size: 18px; font-weight: 700; color: #1a1a1a; }
                .total-amount { color: #6b5f4e; }
                .btn { display: inline-block; padding: 16px 40px; background: #1a1a1a; color: white !important; text-decoration: none; border-radius: 100px; font-size: 15px; font-weight: 600; }
                .btn-wrap { text-align: center; margin: 32px 0; }
                .footer { text-align: center; font-size: 12px; color: #bbb; margin-top: 32px; }
                .badge { display: inline-block; padding: 4px 12px; border-radius: 100px; font-size: 12px; font-weight: 600; }
                .badge--green { background: #ecfdf5; color: #065f46; }
                .badge--yellow { background: #fffbeb; color: #92400e; }
            </style>
        </head>
        <body>
            <div class="card">
                <p class="logo">Ecommerce Dotaciones</p>
                <p class="subtitle">Confirmación de compra</p>

                <h2>¡Gracias por tu compra, ' . htmlspecialchars($usuario->nombre) . '!</h2>
                <p class="info-text">Tu pedido ha sido recibido y está siendo procesado.</p>

                <div style="margin: 20px 0;">
                    <span class="order-number">Pedido ' . htmlspecialchars($orden->numero) . '</span>
                </div>

                <p class="section-label">PRODUCTOS</p>
                <table>
                    <tr>
                        <th>Producto</th>
                        <th>Cant.</th>
                        <th>Precio</th>
                        <th>Total</th>
                    </tr>
                    ' . $itemsHtml . '
                </table>

                <div style="text-align:right; padding: 8px 16px;">
                    <p class="info-text">Subtotal: <strong>$' . number_format($orden->subtotal, 0) . '</strong></p>
                    ' . ($orden->descuento > 0 ? '<p class="info-text" style="color:#7a9e7e;">Descuento: -$' . number_format($orden->descuento, 0) . '</p>' : '') . '
                    <p class="info-text">Envío: <strong>' . ($orden->envio_costo > 0 ? '$' . number_format($orden->envio_costo, 0) : 'Gratis') . '</strong></p>
                    <p class="total-row" style="margin-top:8px;">Total: <span class="total-amount">$' . number_format($orden->total, 0) . ' COP</span></p>
                </div>

                <p class="section-label">MÉTODO DE PAGO</p>
                <p class="info-text">' . htmlspecialchars($metodoPago) . ' — <span class="badge ' . ($estadoPago === 'aprobado' ? 'badge--green' : 'badge--yellow') . '">' . htmlspecialchars($estadoPago) . '</span></p>

                <p class="section-label">DIRECCIÓN DE ENVÍO</p>
                <p class="info-text">' . $direccionTexto . '</p>

                <p class="section-label">ESTADO DEL PEDIDO</p>
                <p class="info-text"><span class="badge badge--yellow">' . htmlspecialchars($orden->estado) . '</span></p>

                <div class="btn-wrap">
                    <a href="' . $frontendUrl . '/mis-pedidos" class="btn">Ver mis pedidos</a>
                </div>

                <p class="footer">Gracias por confiar en Ecommerce Dotaciones.</p>
            </div>
        </body>
        </html>';

        Mail::html($html, function ($message) use ($usuario, $orden) {
            $message->to($usuario->email)
                    ->subject('Pedido confirmado ' . $orden->numero . ' — Ecommerce Dotaciones');
        });
    }
}