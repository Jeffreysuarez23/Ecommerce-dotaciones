<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Lona;
use App\Models\LonaTalla;
use App\Models\HistorialLona;
use Illuminate\Support\Facades\DB;
use Exception;

// CONTROLADOR PARA GESTIONAR LAS LONAS, QUE SON UNA SUBCATEGORÍA DE PRODUCTOS ASOCIADOS A DOTACIONES. PERMITE CREAR, LISTAR, ACTUALIZAR Y DESACTIVAR (SOFT DELETE) LAS LONAS.
class LonaController extends Controller
{
    //  LISTAR TODAS
    public function index()
    {
        $lonas = Lona::with('categoria')->where('activa', 1)->orderBy('id', 'desc')->get();

        return response()->json($lonas);
    }

    //  CREAR
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dotacion_id' => 'required|exists:dotaciones,id',
            'codigo' => 'required|string|max:50|unique:lonas,codigo',
            'tipo_producto' => 'nullable|string|max:80',
            'categoria_id' => 'nullable|exists:categorias,id',
            'estado' => 'nullable|in:nuevo,usado',
            'capacidad_maxima' => 'nullable|integer|min:1'
        ]);

        $validated['estado'] = $validated['estado'] ?? 'nuevo';
        $validated['activa'] = 1;

        $lona = Lona::create($validated);

        return response()->json([
            'message' => 'Lona creada correctamente',
            'data' => $lona
        ], 201);
    }

    //  VER UNA
    public function show($id)
    {
        $lona = Lona::findOrFail($id);

        return response()->json($lona);
    }

    //  ACTUALIZAR
    public function update(Request $request, $id)
    {
        $lona = Lona::findOrFail($id);

        $validated = $request->validate([
            'tipo_producto' => 'nullable|string|max:80',
            'categoria' => 'nullable|string|max:80',
            'estado' => 'nullable|in:nuevo,usado',
            'capacidad_maxima' => 'nullable|integer|min:1'
        ]);

        $lona->update($validated);

        return response()->json([
            'message' => 'Lona actualizada',
            'data' => $lona
        ]);
    }

    //  DESACTIVAR (soft)
    public function destroy($id)
    {
        $lona = Lona::findOrFail($id);

        $lona->activa = 0;
        $lona->save();

        return response()->json([
            'message' => 'Lona desactivada'
        ]);
    }

    // AJUSTAR STOCK MANUALMENTE
    public function ajustarStock(Request $request, $id)
    {
        $lona = Lona::findOrFail($id);

        $validated = $request->validate([
            'talla' => 'required|string|max:10',
            'cantidad_cambio' => 'required|integer'
        ]);

        try {
            DB::beginTransaction();

            $talla = $validated['talla'];
            $cambio = $validated['cantidad_cambio'];

            // 1. Encontrar o crear la talla para esta lona
            $lonaTalla = LonaTalla::firstOrCreate(
                ['lona_id' => $lona->id, 'talla' => $talla],
                ['cantidad' => 0]
            );

            // Evitar inventario negativo
            if ($lonaTalla->cantidad + $cambio < 0) {
                return response()->json([
                    'message' => 'El ajuste resultaría en un stock negativo.'
                ], 400);
            }

            // 2. Actualizar cantidad
            $lonaTalla->cantidad += $cambio;
            $lonaTalla->save();

            // 3. Registrar en historial
            $accion = $cambio > 0 ? 'ingreso' : 'ajuste_manual';
            HistorialLona::create([
                'lona_id' => $lona->id,
                'accion' => $accion,
                'talla' => $talla,
                'cantidad_cambio' => $cambio,
                'cantidad_restante' => $lonaTalla->cantidad,
                'notas' => 'Ajuste manual desde Dashboard'
            ]);

            // 4. Sincronizar hacia la Variante de Producto correspondiente
            $variante = \App\Models\VarianteProducto::where('lona_id', $lona->id)
                ->where('talla', $talla)
                ->first();

            if ($variante) {
                $variante->stock = $lonaTalla->cantidad;
                $variante->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Stock ajustado correctamente',
                'lona_talla' => $lonaTalla
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al ajustar el stock',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}