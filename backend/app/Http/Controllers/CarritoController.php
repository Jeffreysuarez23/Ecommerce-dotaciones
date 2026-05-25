<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Carrito;
use App\Models\CarritoItem;

// CONTROLADOR DE CARRITO DE COMPRAS 
class CarritoController extends Controller
{
    // CREAR CARRITO
    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'nullable|integer',
            'session_id' => 'nullable|string|max:100'
        ]);

        $carrito = Carrito::create($validated);

        return response()->json([
            'message' => 'Carrito creado correctamente',
            'data' => $carrito
        ], 201);
    }

    // AGREGAR ITEM
    public function agregarItem(Request $request, $carrito_id)
    {
        $validated = $request->validate([
            'variante_id' => 'required|integer|exists:variantes_producto,id',
            'lona_id' => 'nullable|integer|exists:lonas,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        $item = CarritoItem::where('carrito_id', $carrito_id)
            ->where('variante_id', $validated['variante_id'])
            ->first();

        if ($item) {
            $item->cantidad += $validated['cantidad'];
            $item->save();
        } else {
            $validated['carrito_id'] = $carrito_id;
            $item = CarritoItem::create($validated);
        }

        return response()->json([
            'message' => 'Item agregado',
            'data' => $item
        ]);
    }

    // LISTAR CARRITO
    public function show($id)
    {
        $carrito = Carrito::with(['items.variante.producto.imagenes'])->findOrFail($id);

        return response()->json($carrito);
    }

    // ACTUALIZAR CANTIDAD
    public function updateItem(Request $request, $id)
    {
        $item = CarritoItem::findOrFail($id);

        $validated = $request->validate([
            'cantidad' => 'required|integer|min:1'
        ]);

        $item->update($validated);

        return response()->json([
            'message' => 'Cantidad actualizada',
            'data' => $item
        ]);
    }

    // ELIMINAR ITEM
    public function destroyItem($id)
    {
        $item = CarritoItem::findOrFail($id);
        $item->delete();

        return response()->json([
            'message' => 'Item eliminado'
        ]);
    }

    // VACIAR CARRITO
    public function vaciar($id)
    {
        CarritoItem::where('carrito_id', $id)->delete();

        return response()->json([
            'message' => 'Carrito vaciado'
        ]);
    }
}