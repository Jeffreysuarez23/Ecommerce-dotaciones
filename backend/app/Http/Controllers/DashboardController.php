<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\Orden;
use App\Models\OrdenItem;
use App\Models\Usuario;
use App\Models\VarianteProducto;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function resumen()
    {
        $ventasTotales = Orden::sum('total');

        $totalOrdenes = Orden::count();

        $totalProductos = Productos::count();

        $totalUsuarios = Usuario::count();

        $ordenesPorEstado = Orden::select(
                'estado',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('estado')
            ->get();

        $productosMasVendidos = OrdenItem::select(
                'lona_id_snapshot',
                DB::raw('SUM(cantidad) as vendidos')
            )
            ->groupBy('lona_id_snapshot')
            ->orderByDesc('vendidos')
            ->limit(10)
            ->get();

        $stockBajo = VarianteProducto::with('producto')
            ->where('stock', '<=', 10)
            ->select(
                'id',
                'producto_id',
                'sku',
                'stock'
            )
            ->get();

        return response()->json([
            'ventas_totales' => $ventasTotales,
            'total_ordenes' => $totalOrdenes,
            'total_productos' => $totalProductos,
            'total_usuarios' => $totalUsuarios,
            'ordenes_por_estado' => $ordenesPorEstado,
            'productos_mas_vendidos' => $productosMasVendidos,
            'stock_bajo' => $stockBajo
        ]);
    }
}