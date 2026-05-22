<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ImagenProducto;
use App\Models\Productos;

/**
 * CONTROLADOR DE IMÁGENES DE PRODUCTO
 *
 * Permite al administrador agregar, listar, reordenar y eliminar
 * imágenes de un producto. Soporta N imágenes por producto y maneja
 * automáticamente la imagen de portada.
 *
 * Rutas sugeridas (en api.php):
 *   GET    /api/productos/{id}/imagenes          → index()
 *   POST   /api/imagenes                         → store()
 *   PUT    /api/imagenes/{id}                    → update()
 *   PUT    /api/imagenes/{id}/portada            → setPortada()
 *   PUT    /api/productos/{id}/imagenes/reorder  → reorder()
 *   DELETE /api/imagenes/{id}                    → destroy()
 */
class ImagenProductoController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // LISTAR imágenes de un producto
    // GET /api/productos/{productoId}/imagenes
    // ──────────────────────────────────────────────────────────────────────────
    public function index($productoId)
    {
        // Verificar que el producto exista
        $producto = Productos::findOrFail($productoId);

        $imagenes = ImagenProducto::where('producto_id', $productoId)
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        return response()->json($imagenes);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CREAR una nueva imagen para un producto
    // POST /api/imagenes
    // Body: { producto_id, url, es_portada?, variante_id?, orden? }
    // ──────────────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'es_portada'  => 'nullable|boolean',
            'variante_id' => 'nullable|exists:variantes_producto,id',
            'orden'       => 'nullable|integer|min:0',
        ]);

        // Si esta imagen va a ser portada, quitarle la portada a las demás del mismo producto
        if ($request->boolean('es_portada', false)) {
            ImagenProducto::where('producto_id', $request->producto_id)
                ->update(['es_portada' => 0]);
        }

        // Calcular orden automático si no se especifica
        $orden = $request->filled('orden')
            ? $request->orden
            : ImagenProducto::where('producto_id', $request->producto_id)->count();

        // Guardar archivo físico en public/images/productos
        $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
        $request->image->move(public_path('images/productos'), $imageName);
        $url = asset('images/productos/' . $imageName);

        $imagen = ImagenProducto::create([
            'producto_id' => $request->producto_id,
            'variante_id' => $request->variante_id,
            'url'         => $url,
            'es_portada'  => $request->boolean('es_portada', false) ? 1 : 0,
            'orden'       => $orden,
        ]);

        return response()->json([
            'message' => 'Imagen guardada correctamente',
            'data'    => $imagen,
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // ACTUALIZAR url u orden de una imagen
    // PUT /api/imagenes/{id}
    // Body: { url?, orden?, variante_id? }
    // ──────────────────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $imagen = ImagenProducto::findOrFail($id);

        $validated = $request->validate([
            'url'         => 'sometimes|string|max:500',
            'orden'       => 'sometimes|integer|min:0',
            'variante_id' => 'nullable|exists:variantes_producto,id',
        ]);

        $imagen->update($validated);

        return response()->json([
            'message' => 'Imagen actualizada',
            'data'    => $imagen,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // MARCAR como portada
    // PUT /api/imagenes/{id}/portada
    // ──────────────────────────────────────────────────────────────────────────
    public function setPortada($id)
    {
        $imagen = ImagenProducto::findOrFail($id);

        // Quitar portada a todas las imágenes del producto
        ImagenProducto::where('producto_id', $imagen->producto_id)
            ->update(['es_portada' => 0]);

        // Poner portada a esta
        $imagen->update(['es_portada' => 1]);

        return response()->json([
            'message' => 'Portada actualizada',
            'data'    => $imagen,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // REORDENAR imágenes de un producto
    // PUT /api/productos/{productoId}/imagenes/reorder
    // Body: { orden: [{ id: 1, orden: 0 }, { id: 2, orden: 1 }, ...] }
    // ──────────────────────────────────────────────────────────────────────────
    public function reorder(Request $request, $productoId)
    {
        $request->validate([
            'orden'         => 'required|array',
            'orden.*.id'    => 'required|integer|exists:imagenes_producto,id',
            'orden.*.orden' => 'required|integer|min:0',
        ]);

        foreach ($request->orden as $item) {
            ImagenProducto::where('id', $item['id'])
                ->where('producto_id', $productoId)   // seguridad: solo imágenes de este producto
                ->update(['orden' => $item['orden']]);
        }

        return response()->json([
            'message' => 'Orden actualizado correctamente',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // ELIMINAR una imagen
    // DELETE /api/imagenes/{id}
    // ──────────────────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $imagen = ImagenProducto::findOrFail($id);
        $productoId = $imagen->producto_id;
        $eraPortada = $imagen->es_portada;

        $imagen->delete();

        // Si era la portada, promover automáticamente la primera imagen restante
        if ($eraPortada) {
            $primera = ImagenProducto::where('producto_id', $productoId)
                ->orderBy('orden')
                ->orderBy('id')
                ->first();

            if ($primera) {
                $primera->update(['es_portada' => 1]);
            }
        }

        return response()->json([
            'message' => 'Imagen eliminada correctamente',
        ]);
    }
}