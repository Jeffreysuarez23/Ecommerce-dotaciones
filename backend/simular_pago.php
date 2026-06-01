<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Orden;
use App\Models\Pago;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrdenConfirmada;

try {
    // Obtener la última orden pendiente
    $orden = Orden::where('estado', 'pendiente')->latest('id')->first();

    if (!$orden) {
        echo "No hay órdenes pendientes para aprobar.\n";
        exit;
    }

    echo "Simulando Webhook de Mercado Pago para la Orden: " . $orden->numero . "...\n";

    // 1. Crear el registro de pago simulado
    $pago = Pago::create([
        'orden_id' => $orden->id,
        'referencia_pasarela' => 'SIM-' . time(),
        'metodo' => 'mercadopago',
        'estado' => 'aprobado',
        'monto' => $orden->total,
        'pagado_en' => now(),
    ]);

    // 2. Actualizar estado de la orden
    $orden->estado = 'confirmada';
    $orden->save();

    // 3. Enviar correo de confirmación
    try {
        App\Http\Controllers\OrdenController::enviarCorreoConfirmacion($orden);
        echo "Correo de confirmación enviado a: " . $orden->usuario->email . "\n";
    } catch (\Exception $e) {
        echo "Aviso: No se pudo enviar el correo, pero la orden se aprobó. Error: " . $e->getMessage() . "\n";
    }

    echo "¡Éxito! La orden " . $orden->numero . " ha sido marcada como PAGADA exitosamente.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
