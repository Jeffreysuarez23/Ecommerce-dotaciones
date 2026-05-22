<?php
namespace App\Http\Controllers;
use App\Models\VarianteProducto;
use App\Models\Lona;
use Illuminate\Http\Request;

// CONTROLADOR DE VARIANTES DE PRODUCTO QUE PERMITE CREAR, ACTUALIZAR Y ELIMINAR LAS VARIANTES ASOCIADAS A UN PRODUCTO. LAS VARIANTES PUEDEN INCLUIR UNA LONA ASOCIADA, SKU, COLOR, TALLA, STOCK Y PRECIO EXTRA.
class VarianteProductoController extends Controller
{
    // LISTAR VARIANTES
    public function index()
    {
        $variantes = VarianteProducto::with('producto')->get();
        return response()->json($variantes);
    }

    // CREAR NUEVA VARIANTE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|integer|exists:productos,id',
            'lona_id' => 'required|integer|exists:lonas,id',
            'sku' => 'nullable|string|max:100',
            'color' => 'required|string|max:50',
            'color_hex' => 'nullable|string|max:20',
            'talla' => 'required|string|max:10',
            'stock' => 'nullable|integer|min:0',
            'precio_extra' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|integer|min:0|max:100'
        ]);

        try {
            if (!empty($validated['lona_id'])) {
                $lona = Lona::findOrFail($validated['lona_id']);
                $espacio_ocupado = VarianteProducto::where('lona_id', $lona->id)->sum('stock');
                $stock_a_agregar = $validated['stock'] ?? 0;

                if ($lona->capacidad_maxima !== null && ($espacio_ocupado + $stock_a_agregar) > $lona->capacidad_maxima) {
                    return response()->json(['message' => 'La lona ha alcanzado su capacidad máxima ('.$lona->capacidad_maxima.' prendas). Espacio disponible: ' . ($lona->capacidad_maxima - $espacio_ocupado)], 400);
                }
            }

            // Si no se proporciona stock, establecer a 0
            if (!isset($validated['stock'])) {
                $validated['stock'] = 0;
            }

            $variante = VarianteProducto::create($validated);

            // Sincronizar stock hacia la Lona física
            if ($variante->lona_id && $variante->stock > 0) {
                $lonaTalla = \App\Models\LonaTalla::firstOrCreate(
                    ['lona_id' => $variante->lona_id, 'talla' => $variante->talla],
                    ['cantidad' => 0]
                );
                
                $lonaTalla->cantidad += $variante->stock;
                $lonaTalla->save();

                \App\Models\HistorialLona::create([
                    'lona_id' => $variante->lona_id,
                    'talla' => $variante->talla,
                    'cantidad_cambio' => $variante->stock,
                    'accion' => 'ingreso',
                    'notas' => 'Creación de nueva variable de producto (SKU: ' . ($variante->sku ?? 'N/A') . ')'
                ]);
            }

            return response()->json([
                'message' => 'Variante creada correctamente',
                'data' => $variante
            ], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => 'Error al crear la variante. Verifica los datos.',
                'details' => $e->getMessage()
            ], 400);
        }
    }

    // ACTUALIZAR
public function update(Request $request, $id)
{
    $variante = VarianteProducto::findOrFail($id);

    $validated = $request->validate([
        'lona_id' => 'required|integer|exists:lonas,id',
        'sku' => 'nullable|string|max:100',
        'color' => 'sometimes|string|max:50',
        'color_hex' => 'nullable|string|max:20',
        'talla' => 'sometimes|string|max:10',
        'stock' => 'nullable|integer|min:0',
        'precio_extra' => 'nullable|numeric|min:0',
        'descuento' => 'nullable|integer|min:0|max:100'
    ]);

    $oldStock = $variante->stock;
    $newStock = $validated['stock'] ?? $oldStock;

    if (!empty($validated['lona_id'])) {
        $lona = Lona::findOrFail($validated['lona_id']);
        
        // Si cambia de lona, el stock a validar es todo el nuevo stock.
        // Si se mantiene en la misma lona, el stock a validar es la diferencia.
        $espacio_ocupado = VarianteProducto::where('lona_id', $lona->id)->sum('stock');
        
        if ($validated['lona_id'] == $variante->lona_id) {
            $espacio_ocupado -= $oldStock; // Restamos el viejo para no contarlo doble
        }

        if ($lona->capacidad_maxima !== null && ($espacio_ocupado + $newStock) > $lona->capacidad_maxima) {
            return response()->json(['message' => 'La lona ha alcanzado su capacidad máxima ('.$lona->capacidad_maxima.' prendas). Espacio disponible: ' . ($lona->capacidad_maxima - $espacio_ocupado)], 400);
        }
    }

    $oldStock = $variante->stock;
    $oldLonaId = $variante->lona_id;
    $oldTalla = $variante->talla;

    $variante->update($validated);

    $lonaChanged = $oldLonaId != $variante->lona_id;
    $tallaChanged = $oldTalla != $variante->talla;
    $stockChanged = $oldStock != $variante->stock;

    if ($lonaChanged || $tallaChanged || $stockChanged) {
        // Restar contribución anterior
        if ($oldLonaId) {
            $lt = \App\Models\LonaTalla::where('lona_id', $oldLonaId)->where('talla', $oldTalla)->first();
            if ($lt) {
                $lt->cantidad -= $oldStock;
                if ($lt->cantidad < 0) $lt->cantidad = 0;
                $lt->save();
                if ($lonaChanged || $tallaChanged) {
                    \App\Models\HistorialLona::create([
                        'lona_id' => $oldLonaId,
                        'talla' => $oldTalla,
                        'cantidad_cambio' => -$oldStock,
                        'accion' => 'movimiento',
                        'notas' => 'Variable removida o reubicada (SKU: ' . ($variante->sku ?? 'N/A') . ')'
                    ]);
                }
            }
        }
        
        // Sumar nueva contribución
        if ($variante->lona_id) {
            $lt = \App\Models\LonaTalla::firstOrCreate(
                ['lona_id' => $variante->lona_id, 'talla' => $variante->talla],
                ['cantidad' => 0]
            );
            $lt->cantidad += $variante->stock;
            $lt->save();

            if ($lonaChanged || $tallaChanged) {
                \App\Models\HistorialLona::create([
                    'lona_id' => $variante->lona_id,
                    'talla' => $variante->talla,
                    'cantidad_cambio' => $variante->stock,
                    'accion' => 'ingreso',
                    'notas' => 'Variable reubicada a esta lona/talla (SKU: ' . ($variante->sku ?? 'N/A') . ')'
                ]);
            } elseif ($stockChanged) {
                $diff = $variante->stock - $oldStock;
                \App\Models\HistorialLona::create([
                    'lona_id' => $variante->lona_id,
                    'talla' => $variante->talla,
                    'cantidad_cambio' => $diff,
                    'accion' => $diff > 0 ? 'ingreso' : 'ajuste_manual',
                    'notas' => 'Actualización de stock desde el producto (SKU: ' . ($variante->sku ?? 'N/A') . ')'
                ]);
            }
        }
    }

    return response()->json([
        'message' => 'Variante actualizada',
        'data' => $variante
    ]);
}


// ELIMINAR
public function destroy($id)
{
    $variante = VarianteProducto::findOrFail($id);

    $variante->delete();

    return response()->json([
        'message' => 'Variante eliminada'
    ]);
}
}