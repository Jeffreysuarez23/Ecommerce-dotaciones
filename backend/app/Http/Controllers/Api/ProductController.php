<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Productos;
use Illuminate\Support\Str;



// CONTROLADOR DE PRODUCTOS QUE PERMITE CREAR, LISTAR, MOSTRAR DETALLES, ACTUALIZAR Y ELIMINAR PRODUCTOS. CADA PRODUCTO TIENE UN NOMBRE, PRECIOS PARA MINORISTA Y MAYORISTA, Y UNA RELACIÓN CON VARIANTES (COMO COLOR O TALLA).
class ProductController extends Controller
{
    // CREAR UN NUEVO PRODUCTO
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio_minorista' => 'required|numeric|min:0',
            'precio_mayorista' => 'required|numeric|min:0',
            'min_cantidad_mayorista' => 'nullable|integer|min:1',
            'categoria_id' => 'required|integer|exists:categorias,id',
            'lona_id' => 'required|integer|exists:lonas,id',
            'publicado' => 'nullable|boolean',
            'permitir_sin_stock' => 'nullable|boolean',
            'destacado' => 'nullable|boolean'
        ]);

        $lona = \App\Models\Lona::findOrFail($request->lona_id);
        $espacio_ocupado = \App\Models\VarianteProducto::where('lona_id', $lona->id)->count();
        if ($lona->capacidad_maxima !== null && $espacio_ocupado >= $lona->capacidad_maxima) {
            return response()->json(['message' => 'La lona seleccionada ha alcanzado su capacidad máxima ('.$lona->capacidad_maxima.' variables).'], 400);
        }

        $producto = Productos::create([
            'nombre' => $request->nombre,
            'slug' => Str::slug($request->nombre),
            'descripcion' => $request->descripcion,
            'precio_minorista' => $request->precio_minorista,
            'precio_mayorista' => $request->precio_mayorista,
            'min_cantidad_mayorista' => $request->min_cantidad_mayorista ?? 12,
            'categoria_id' => $request->categoria_id,
            'destacado' => $request->destacado ?? 0,
            'publicado' => $request->has('publicado') ? $request->publicado : 1,
            'permitir_sin_stock' => $request->has('permitir_sin_stock') ? $request->permitir_sin_stock : 1
        ]);

        \App\Models\VarianteProducto::create([
            'producto_id' => $producto->id,
            'lona_id' => $request->lona_id,
            'sku' => Str::upper(substr(Str::slug($request->nombre, ''), 0, 4)) . '-' . rand(1000, 9999),
            'color' => 'Defecto',
            'talla' => 'Única',
            'stock' => 0
        ]);

        return response()->json($producto, 201);
    }
 // LISTAR TODOS LOS PRODUCTOS
    public function index()
    {
        return Productos::with(['variantes.lona.tallas', 'imagenes', 'categoria'])->get();
    }
 //  MOSTRAR UN PRODUCTO CON SUS VARIANTES E IMÁGENES
    public function show($id)
    {
        $producto = Productos::with(['variantes.lona.tallas', 'imagenes', 'categoria'])->findOrFail($id);

        return response()->json([
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'descripcion' => $producto->descripcion,
            'categoria_id' => $producto->categoria_id,
            'destacado' => $producto->destacado,
            'publicado' => $producto->publicado,
            'permitir_sin_stock' => $producto->permitir_sin_stock,
            'precio_minorista' => $producto->precio_minorista,
            'precio_mayorista' => $producto->precio_mayorista,
            'imagenes' => $producto->imagenes,
            'variantes' => $producto->variantes->map(function ($v) {
                return [
                    'id' => $v->id,
                    'sku' => $v->sku,
                    'color' => $v->color,
                    'color_hex' => $v->color_hex,
                    'talla' => $v->talla,
                    'stock' => $v->stock,
                    'precio_extra' => $v->precio_extra,
                    'descuento' => $v->descuento,
                    'lona' => $v->lona ? [
                        'color' => $v->lona->color,
                        'tallas' => $v->lona->tallas
                    ] : null
                ];
            })
        ]);
    }
    // ACTUALIZAR UN PRODUCTO EXISTENTE
public function update(Request $request, $id)
{
    $producto = Productos::findOrFail($id);

    $validated = $request->validate([
        'nombre' => 'sometimes|string|max:150',
        'descripcion' => 'nullable|string',
        'precio_minorista' => 'sometimes|numeric|min:0',
        'precio_mayorista' => 'sometimes|numeric|min:0',
        'min_cantidad_mayorista' => 'nullable|integer|min:1',
        'categoria_id' => 'nullable|integer|exists:categorias,id',
        'publicado' => 'nullable|boolean',
        'permitir_sin_stock' => 'nullable|boolean',
        'destacado' => 'nullable|boolean'
    ]);

    if(isset($validated['nombre'])){
        $validated['slug'] = Str::slug($validated['nombre']);
    }

    $producto->update($validated);

    return response()->json([
        'message' => 'Producto actualizado',
        'data' => $producto
    ]);
}


//  ELIMINAR UN PRODUCTO
public function destroy($id)
{
    $producto = Productos::findOrFail($id);
    
    // Eliminar las variantes asociadas primero (Soft Delete)
    $producto->variantes()->delete();

    // Luego eliminar el producto principal (Soft Delete)
    $producto->delete();

    return response()->json([
        'message' => 'Producto eliminado'
    ]);
}
}