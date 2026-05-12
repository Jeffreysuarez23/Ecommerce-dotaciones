<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

// MODELO ORDEN ITEM
class OrdenItem extends Model
{
    protected $table = 'orden_items';

    public $timestamps = false;

    protected $fillable = [
        'orden_id',
        'variante_id',
        'lona_id_snapshot',
        'cantidad',
        'precio_unitario',
        'total_linea'
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'total_linea' => 'decimal:2',
    ];

    // RELACIÓN ORDEN
    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    // RELACIÓN VARIANTE
    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_id');
    }
}