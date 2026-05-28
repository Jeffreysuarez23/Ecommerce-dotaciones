<?php
namespace App\Http\Controllers;
use App\Models\Cupon;
use Illuminate\Http\Request;

// CUPONES
class CuponController extends Controller
{
   // CRUD BÁSICO 
    public function index()
    {
        return response()->json(
            Cupon::orderBy('id', 'desc')->get()
        );
    }
    // CREAR Y VALIDAR CUPÓN
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:cupones,codigo',
            'tipo' => 'required|in:porcentaje,fijo',
            'valor' => 'required|numeric|min:1',
            'monto_minimo_pedido' => 'nullable|numeric|min:0',
            'limite_usos' => 'nullable|integer|min:1',
            'expira_en' => 'nullable|date'
        ]);

        $cupon = Cupon::create([
            'codigo' => strtoupper($request->codigo),
            'tipo' => $request->tipo,
            'valor' => $request->valor,
            'monto_minimo_pedido' => $request->monto_minimo_pedido ?? 0,
            'limite_usos' => $request->limite_usos,
            'usos_actuales' => 0,
            'activo' => true,
            'expira_en' => $request->expira_en
        ]);

        return response()->json([
            'message' => 'Cupón creado correctamente',
            'data' => $cupon
        ], 201);
    }
    // MOSTRAR UN CUPÓN
    public function show($id)
    {
        return response()->json(
            Cupon::findOrFail($id)
        );
    }
    // ACTUALIZAR UN CUPÓN
    public function update(Request $request, $id)
    {
        $cupon = Cupon::findOrFail($id);

        $request->validate([
            'codigo' => 'required|string|max:50|unique:cupones,codigo,' . $id,
            'tipo' => 'required|in:porcentaje,fijo',
            'valor' => 'required|numeric|min:1'
        ]);

        $cupon->update([
            'codigo' => strtoupper($request->codigo),
            'tipo' => $request->tipo,
            'valor' => $request->valor,
            'monto_minimo_pedido' => $request->monto_minimo_pedido ?? 0,
            'limite_usos' => $request->limite_usos,
            'activo' => $request->activo ?? true,
            'expira_en' => $request->expira_en
        ]);

        return response()->json([
            'message' => 'Cupón actualizado'
        ]);
    }
    // ELIMINAR UN CUPÓN
    public function destroy($id)
    {
        Cupon::destroy($id);

        return response()->json([
            'message' => 'Cupón eliminado'
        ]);
    }
    //
    public function validar(Request $request)
    {
        $request->validate([
            'codigo' => 'required',
            'subtotal' => 'required|numeric|min:1'
        ]);

        $cupon = Cupon::where(
            'codigo',
            strtoupper($request->codigo)
        )->first();

        if (!$cupon) {
            return response()->json([
                'message' => 'Cupón no existe'
            ], 404);
        }

        if (!$cupon->activo) {
            return response()->json([
                'message' => 'Cupón inactivo'
            ], 400);
        }

        if (
            $cupon->expira_en &&
            now()->gt($cupon->expira_en)
        ) {
            return response()->json([
                'message' => 'Cupón expirado'
            ], 400);
        }

        if (
            $cupon->limite_usos &&
            $cupon->usos_actuales >= $cupon->limite_usos
        ) {
            return response()->json([
                'message' => 'Límite de usos alcanzado'
            ], 400);
        }

        if (
            $request->subtotal <
            $cupon->monto_minimo_pedido
        ) {
            return response()->json([
                'message' => 'No cumple el monto mínimo'
            ], 400);
        }

        $descuento = 0;

        if ($cupon->tipo === 'porcentaje') {

            $descuento =
                ($request->subtotal * $cupon->valor) / 100;

        } else {

            $descuento = $cupon->valor;
        }

        return response()->json([
            'valido' => true,
            'cupon' => $cupon->codigo,
            'descuento' => round($descuento, 2)
        ]);
    }
    // APLICAR CUPÓN A UN CARRITO
    public function aplicar(Request $request)
    {
        $request->validate([
            'carrito_id' => 'required|exists:carritos,id',
            'codigo' => 'required'
        ]);

        $cupon = Cupon::where(
            'codigo',
            strtoupper($request->codigo)
        )->first();

        if (!$cupon) {
            return response()->json([
                'message' => 'Cupón no encontrado'
            ], 404);
        }

        if (!$cupon->activo) {
            return response()->json([
                'message' => 'Cupón inactivo'
            ], 400);
        }

        $carrito = \App\Models\Carrito::findOrFail(
            $request->carrito_id
        );

        $carrito->cupon_id = $cupon->id;
        $carrito->save();

        return response()->json([
            'message' => 'Cupón aplicado correctamente',
            'cupon' => $cupon->codigo,
            'carrito_id' => $carrito->id
        ]);
    }


}