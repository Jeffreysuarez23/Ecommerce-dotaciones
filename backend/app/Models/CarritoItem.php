<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

// CARRITO ITEM
class CarritoItem extends Model
{
    protected $table = 'carrito_items';
    public $timestamps = false;

    protected $fillable = [
        'carrito_id',
        'variante_id',
        'lona_id',
        'cantidad'
    ];

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_id');
    }
}