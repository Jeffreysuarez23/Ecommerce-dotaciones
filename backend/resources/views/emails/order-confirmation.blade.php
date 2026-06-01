<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido</title>
</head>
<body style="margin:0;padding:0;background-color:#f5f3ef;font-family:'Segoe UI','Helvetica Neue',Arial,sans-serif;">

    <!-- Wrapper -->
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f3ef;padding:40px 0;">
        <tr>
            <td align="center">

                <!-- Main Card -->
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#1a1a1a 0%,#2d2d2d 100%);padding:40px 40px 32px;text-align:center;">
                            <h1 style="margin:0;font-size:28px;font-weight:700;color:#ffffff;letter-spacing:-0.5px;">
                                ¡Pedido Confirmado!
                            </h1>
                            <p style="margin:8px 0 0;font-size:14px;color:rgba(255,255,255,0.65);">
                                Gracias por tu compra, {{ $orden->usuario->nombre ?? 'Cliente' }}
                            </p>
                        </td>
                    </tr>

                    <!-- Order Number Badge -->
                    <tr>
                        <td style="padding:24px 40px 0;text-align:center;">
                            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
                                <tr>
                                    <td style="background-color:#f0ebe3;border-radius:8px;padding:12px 24px;">
                                        <span style="font-size:12px;color:#888;text-transform:uppercase;letter-spacing:0.1em;display:block;">Número de pedido</span>
                                        <span style="font-size:22px;font-weight:700;color:#1a1a1a;display:block;margin-top:2px;">#{{ $orden->numero }}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Order Status -->
                    <tr>
                        <td style="padding:20px 40px 0;text-align:center;">
                            @php
                                $estadoLabel = match($orden->estado) {
                                    'pagado' => 'Pagado',
                                    'confirmada' => 'Confirmado',
                                    'pendiente' => 'Pendiente de pago',
                                    default => ucfirst($orden->estado)
                                };
                                $estadoColor = match($orden->estado) {
                                    'pagado', 'confirmada' => '#7a9e7e',
                                    'pendiente' => '#e8a838',
                                    default => '#888'
                                };
                            @endphp
                            <span style="display:inline-block;background-color:{{ $estadoColor }};color:#fff;padding:6px 16px;border-radius:20px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">
                                {{ $estadoLabel }}
                            </span>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding:24px 40px 0;">
                            <div style="border-top:1px solid #ede8de;"></div>
                        </td>
                    </tr>

                    <!-- Products Section -->
                    <tr>
                        <td style="padding:24px 40px 0;">
                            <h2 style="margin:0 0 16px;font-size:14px;font-weight:600;color:#888;text-transform:uppercase;letter-spacing:0.1em;">
                                Artículos del Pedido
                            </h2>

                            <table width="100%" cellpadding="0" cellspacing="0">
                                @foreach($orden->items as $item)
                                @php
                                    $producto = $item->variante->producto ?? null;
                                    $imagenCid = null;
                                    if ($producto && $producto->imagenes && $producto->imagenes->count() > 0) {
                                        $cover = $producto->imagenes->firstWhere('es_portada', 1);
                                        $imgRecord = $cover ?: $producto->imagenes->first();
                                        // Extraer ruta relativa de la URL almacenada (ej: /images/productos/xxx.png)
                                        $urlPath = parse_url($imgRecord->url, PHP_URL_PATH);
                                        $localPath = public_path($urlPath);
                                        if ($urlPath && file_exists($localPath)) {
                                            $imagenCid = $message->embed($localPath);
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #f5f3ef;vertical-align:top;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                @if($imagenCid)
                                                <td width="80" style="vertical-align:top;padding-right:14px;">
                                                    <img src="{{ $imagenCid }}" alt="{{ $producto->nombre ?? 'Producto' }}" width="70" height="70" style="width:70px;height:70px;object-fit:cover;border-radius:8px;border:1px solid #ede8de;display:block;" />
                                                </td>
                                                @endif
                                                <td style="vertical-align:top;">
                                                    <p style="margin:0;font-size:15px;font-weight:600;color:#1a1a1a;">
                                                        {{ $producto->nombre ?? 'Producto' }}
                                                    </p>
                                                    <p style="margin:4px 0 0;font-size:13px;color:#888;">
                                                        {{ $item->variante->color ?? '' }} - {{ $item->variante->talla ?? '' }}
                                                    </p>
                                                    <p style="margin:4px 0 0;font-size:13px;color:#888;">
                                                        Cantidad: {{ $item->cantidad }}
                                                    </p>
                                                </td>
                                                <td width="120" style="text-align:right;vertical-align:top;">
                                                    <p style="margin:0;font-size:15px;font-weight:600;color:#1a1a1a;">
                                                        ${{ number_format($item->total_linea, 0, ',', '.') }}
                                                    </p>
                                                    <p style="margin:4px 0 0;font-size:12px;color:#aaa;">
                                                        c/u ${{ number_format($item->precio_unitario, 0, ',', '.') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>

                    <!-- Totals -->
                    <tr>
                        <td style="padding:20px 40px 0;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding:8px 0;font-size:14px;color:#666;">Subtotal</td>
                                    <td style="padding:8px 0;font-size:14px;color:#666;text-align:right;">
                                        ${{ number_format($orden->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @if($orden->descuento > 0)
                                <tr>
                                    <td style="padding:8px 0;font-size:14px;color:#7a9e7e;">Descuento</td>
                                    <td style="padding:8px 0;font-size:14px;color:#7a9e7e;text-align:right;">
                                        -${{ number_format($orden->descuento, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding:8px 0;font-size:14px;color:#666;">Envío</td>
                                    <td style="padding:8px 0;font-size:14px;color:#666;text-align:right;">
                                        @if($orden->envio_costo > 0)
                                            ${{ number_format($orden->envio_costo, 0, ',', '.') }}
                                        @else
                                            <span style="color:#7a9e7e;font-weight:600;">Gratis</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding:8px 0 0;">
                                        <div style="border-top:2px solid #1a1a1a;"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;font-size:18px;font-weight:700;color:#1a1a1a;">Total</td>
                                    <td style="padding:12px 0;font-size:18px;font-weight:700;color:#1a1a1a;text-align:right;">
                                        ${{ number_format($orden->total, 0, ',', '.') }} COP
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding:8px 40px 0;">
                            <div style="border-top:1px solid #ede8de;"></div>
                        </td>
                    </tr>

                    <!-- Payment Method -->
                    <tr>
                        <td style="padding:20px 40px 0;">
                            <h2 style="margin:0 0 8px;font-size:14px;font-weight:600;color:#888;text-transform:uppercase;letter-spacing:0.1em;">
                                Método de Pago
                            </h2>
                            @php
                                $pago = $orden->pago->first();
                                $metodoLabel = $pago ? ucfirst($pago->metodo) : 'N/A';
                            @endphp
                            <p style="margin:0;font-size:15px;color:#1a1a1a;">{{ $metodoLabel }}</p>
                            @if($pago && $pago->referencia_pasarela)
                                <p style="margin:4px 0 0;font-size:12px;color:#aaa;">Ref: {{ $pago->referencia_pasarela }}</p>
                            @endif
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding:20px 40px 0;">
                            <div style="border-top:1px solid #ede8de;"></div>
                        </td>
                    </tr>

                    <!-- Shipping Address -->
                    <tr>
                        <td style="padding:20px 40px 0;">
                            <h2 style="margin:0 0 8px;font-size:14px;font-weight:600;color:#888;text-transform:uppercase;letter-spacing:0.1em;">
                                Dirección de Envío
                            </h2>
                            @if($orden->direccion)
                            <p style="margin:0;font-size:15px;color:#1a1a1a;line-height:1.6;">
                                <strong>{{ $orden->direccion->nombre_recibe }}</strong><br>
                                {{ $orden->direccion->direccion }}<br>
                                {{ $orden->direccion->ciudad }}, {{ $orden->direccion->departamento }}
                                @if($orden->direccion->codigo_postal)
                                    {{ $orden->direccion->codigo_postal }}
                                @endif
                                @if($orden->direccion->telefono)
                                    <br>Tel: {{ $orden->direccion->telefono }}
                                @endif
                                @if($orden->direccion->referencia)
                                    <br><span style="color:#888;">Ref: {{ $orden->direccion->referencia }}</span>
                                @endif
                            </p>
                            @endif
                        </td>
                    </tr>

                    @if($orden->notas_cliente)
                    <!-- Customer Notes -->
                    <tr>
                        <td style="padding:20px 40px 0;">
                            <div style="border-top:1px solid #ede8de;margin-bottom:16px;"></div>
                            <h2 style="margin:0 0 8px;font-size:14px;font-weight:600;color:#888;text-transform:uppercase;letter-spacing:0.1em;">
                                Notas del Pedido
                            </h2>
                            <p style="margin:0;font-size:14px;color:#666;font-style:italic;">
                                "{{ $orden->notas_cliente }}"
                            </p>
                        </td>
                    </tr>
                    @endif

                    <!-- CTA Button -->
                    <tr>
                        <td style="padding:32px 40px;text-align:center;">
                            <a href="{{ env('FRONTEND_URL', 'http://localhost:5173') }}/mis-pedidos"
                               style="display:inline-block;background-color:#1a1a1a;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:8px;font-size:14px;font-weight:600;letter-spacing:0.02em;">
                                Ver Mis Pedidos →
                            </a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#faf8f4;padding:24px 40px;border-top:1px solid #ede8de;text-align:center;">
                            <p style="margin:0;font-size:12px;color:#aaa;">
                                Este correo fue enviado automáticamente. Si tienes alguna duda, contáctanos.
                            </p>
                            <p style="margin:8px 0 0;font-size:12px;color:#ccc;">
                                © {{ date('Y') }} {{ env('MAIL_FROM_NAME', 'Ecommerce Dotaciones') }}. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- /Main Card -->

            </td>
        </tr>
    </table>

</body>
</html>
