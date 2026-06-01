<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// MODELO DE VARIANTES DE PRODUCTO, QUE REPRESENTA LAS DIFERENTES VARIANTES DE UN PRODUCTO PRINCIPAL, ASOCIADAS A UNA LONA ESPECÍFICA. INCLUYE CAMPOS PARA SKU, COLOR, TALLA, STOCK Y PRECIO EXTRA.
class VarianteProducto extends Model
{
    use SoftDeletes;

    const DELETED_AT = 'eliminado_en';

    protected $table = 'variantes_producto';

    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'lona_id',
        'sku',
        'color',
        'color_hex',
        'talla',
        'stock',
        'precio_extra',
        'descuento'
    ];
    
    // RELACIONES CON PRODUCTO PRINCIPAL
    public function producto()
    {
        return $this->belongsTo(Productos::class, 'producto_id');
    }
    
    // RELACIÓN CON LONA ESPECÍFICA
    public function lona()
    {
        return $this->belongsTo(Lona::class, 'lona_id');
    }
}