<?php

// API Routes
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\VarianteProductoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImagenProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DotacionController;
use App\Http\Controllers\LonaController;
use App\Http\Controllers\LonaTallaController;
use App\Http\Controllers\HistorialLonaController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\Api\PagoController;
use App\Http\Controllers\API\EnvioController;
use App\Http\Controllers\Api\DevolucionController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\CuponController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\ContactoController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\TrabajadorController;

// ─── RUTAS PÚBLICAS ───

// PRODUCTOS (lectura pública)
Route::get('/productos', [ProductController::class, 'index']);
Route::get('/productos/{id}', [ProductController::class, 'show']);

// CATEGORÍAS (lectura pública)
Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/categorias/{id}', [CategoriaController::class, 'show']);

// CUPONES (validación pública)
Route::post('cupones/validar', [CuponController::class, 'validar']);
Route::post('cupones/aplicar', [CuponController::class, 'aplicar']);

// CARRITO (público: permite usuarios no autenticados con session_id)
Route::post('/carritos', [CarritoController::class, 'store']);
Route::post('/carritos/{id}/items', [CarritoController::class, 'agregarItem']);
Route::get('/carritos/{id}', [CarritoController::class, 'show']);
Route::put('/carrito-items/{id}', [CarritoController::class, 'updateItem']);
Route::delete('/carrito-items/{id}', [CarritoController::class, 'destroyItem']);
Route::delete('/carritos/{id}/vaciar', [CarritoController::class, 'vaciar']);

// AUTENTICACIÓN (públicas)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/verify-email/{id}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/resend-verification', [AuthController::class, 'resendVerification']);

// Webhook de PayPal (público)
Route::post('/paypal/webhook', [App\Http\Controllers\Api\PayPalController::class, 'webhook']);

// TRACKING público
Route::get('/tracking/{guia}', [EnvioController::class, 'tracking']);

// CONTACTO
Route::post('/contactos', [ContactoController::class, 'store']);

// TEST
Route::get('/test', function () {
    return response()->json([
        'message' => 'Servidor funcionando correctamente'
    ]);
});

// ─── RUTAS PROTEGIDAS (requieren autenticación) ───
Route::middleware('auth:sanctum')->group(function () {

    // ORDENES (usuario autenticado)
    Route::post('/ordenes/crear', [OrdenController::class, 'crearDesdeCarrito']);
    Route::get('/mis-pedidos', [OrdenController::class, 'misPedidos']);
    Route::get('/ordenes/{id}', [OrdenController::class, 'show']);
    Route::put('/ordenes/{id}/cancelar', [OrdenController::class, 'cancelar']);

    // DIRECCIONES (del usuario autenticado)
    Route::get('/direcciones', [DireccionController::class, 'index']);
    Route::post('/direcciones', [DireccionController::class, 'store']);
    Route::get('/direcciones/{id}', [DireccionController::class, 'show']);
    Route::put('/direcciones/{id}', [DireccionController::class, 'update']);
    Route::delete('/direcciones/{id}', [DireccionController::class, 'destroy']);

    // Rutas de PayPal (protegidas)
    Route::post('/paypal/create-order', [App\Http\Controllers\Api\PayPalController::class, 'createOrder']);
    Route::post('/paypal/capture-order', [App\Http\Controllers\Api\PayPalController::class, 'captureOrder']);

    // PERFIL
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::delete('/profile', [AuthController::class, 'deleteProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // DEVOLUCIONES (usuario autenticado puede crear)
    Route::post('/devoluciones', [DevolucionController::class, 'store']);
});

// ─── RUTAS DE ADMINISTRADOR (requieren autenticación + rol admin) ───
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->group(function () {

    // PRODUCTOS (CRUD admin)
    Route::post('/productos', [ProductController::class, 'store']);
    Route::put('/productos/{id}', [ProductController::class, 'update']);
    Route::delete('/productos/{id}', [ProductController::class, 'destroy']);

    // VARIANTES
    Route::get('/variantes', [VarianteProductoController::class, 'index']);
    Route::post('/variantes', [VarianteProductoController::class, 'store']);
    Route::put('/variantes/{id}', [VarianteProductoController::class, 'update']);
    Route::delete('/variantes/{id}', [VarianteProductoController::class, 'destroy']);

    // IMÁGENES
    Route::get('/productos/{id}/imagenes', [ImagenProductoController::class, 'index']);
    Route::post('/imagenes', [ImagenProductoController::class, 'store']);
    Route::put('/imagenes/{id}', [ImagenProductoController::class, 'update']);
    Route::put('/imagenes/{id}/portada', [ImagenProductoController::class, 'setPortada']);
    Route::put('/productos/{id}/imagenes/reorder', [ImagenProductoController::class, 'reorder']);
    Route::delete('/imagenes/{id}', [ImagenProductoController::class, 'destroy']);

    // CATEGORÍAS (CRUD admin)
    Route::post('/categorias', [CategoriaController::class, 'store']);
    Route::put('/categorias/{id}', [CategoriaController::class, 'update']);
    Route::post('/categorias/{id}/imagen', [CategoriaController::class, 'uploadImagen']);
    Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy']);

    // CUPONES (CRUD admin)
    Route::apiResource('cupones', CuponController::class);

    // DOTACIONES
    Route::get('/dotaciones', [DotacionController::class, 'index']);
    Route::post('/dotaciones', [DotacionController::class, 'store']);
    Route::get('/dotaciones/{id}', [DotacionController::class, 'show']);
    Route::put('/dotaciones/{id}', [DotacionController::class, 'update']);
    Route::delete('/dotaciones/{id}', [DotacionController::class, 'destroy']);

    // LONAS
    Route::get('/lonas', [LonaController::class, 'index']);
    Route::post('/lonas', [LonaController::class, 'store']);
    Route::get('/lonas/{id}', [LonaController::class, 'show']);
    Route::put('/lonas/{id}', [LonaController::class, 'update']);
    Route::delete('/lonas/{id}', [LonaController::class, 'destroy']);
    Route::post('/lonas/{id}/ajustar-stock', [LonaController::class, 'ajustarStock']);

    // LONA TALLAS
    Route::get('/lona-tallas', [LonaTallaController::class, 'index']);
    Route::post('/lona-tallas', [LonaTallaController::class, 'store']);
    Route::put('/lona-tallas/{id}', [LonaTallaController::class, 'update']);
    Route::delete('/lona-tallas/{id}', [LonaTallaController::class, 'destroy']);

    // HISTORIAL LONA
    Route::get('/historial-lonas', [HistorialLonaController::class, 'index']);
    Route::post('/historial-lonas', [HistorialLonaController::class, 'store']);
    Route::get('/historial-lonas/{id}', [HistorialLonaController::class, 'show']);

    // ORDENES (admin)
    Route::get('/ordenes', [OrdenController::class, 'index']);
    Route::put('/ordenes/{id}/estado', [OrdenController::class, 'cambiarEstado']);

    // PAGOS (admin)
    Route::get('/pagos', [PagoController::class, 'index']);
    Route::get('/pagos/{id}', [PagoController::class, 'show']);
    Route::post('/pagos', [PagoController::class, 'registrar']);
    Route::put('/pagos/{id}/aprobar', [PagoController::class, 'aprobar']);
    Route::put('/pagos/{id}/rechazar', [PagoController::class, 'rechazar']);
    Route::put('/pagos/{id}/reembolso', [PagoController::class, 'reembolso']);

    // ENVÍOS (admin)
    Route::post('/envios', [EnvioController::class, 'store']);
    Route::get('/envios', [EnvioController::class, 'index']);
    Route::get('/envios/{id}', [EnvioController::class, 'show']);
    Route::put('/envios/{id}/estado', [EnvioController::class, 'cambiarEstado']);

    // DEVOLUCIONES (admin)
    Route::get('/devoluciones', [DevolucionController::class, 'index']);
    Route::get('/devoluciones/{id}', [DevolucionController::class, 'show']);
    Route::put('/devoluciones/{id}/aprobar', [DevolucionController::class, 'aprobar']);
    Route::put('/devoluciones/{id}/rechazar', [DevolucionController::class, 'rechazar']);
    Route::put('/devoluciones/{id}/resolver', [DevolucionController::class, 'resolver']);

    // NOTIFICACIONES
    Route::get('notificaciones', [NotificacionController::class, 'index']);
    Route::post('notificaciones', [NotificacionController::class, 'store']);
    Route::put('notificaciones/{id}/leer', [NotificacionController::class, 'marcarLeido']);

    // DASHBOARD
    Route::get('dashboard/resumen', [DashboardController::class, 'resumen']);

    // USUARIOS (Dashboard)
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::post('/usuarios', [UsuarioController::class, 'store']);
    Route::put('/usuarios/{id}/rol', [UsuarioController::class, 'updateRole']);
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);

    // TRABAJADORES (Admin)
    Route::apiResource('trabajadores', TrabajadorController::class);
});

// MERCADO PAGO (deshabilitado - controlador pendiente de implementar)
// Route::post('/mercadopago/webhook', [MercadoPagoController::class, 'webhook']);
// Route::get('/mercadopago/retorno', [MercadoPagoController::class, 'retorno']);