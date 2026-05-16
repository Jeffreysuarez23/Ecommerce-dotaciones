<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

// MODELO DE IMÁGENES DE PRODUCTO, QUE REPRESENTA LAS IMÁGENES ASOCIADAS A LOS PRODUCTOS Y SUS VARIANTES. INCLUYE CAMPOS PARA URL, SI ES PORTADA Y EL ORDEN DE LAS IMÁGENES.
class ImagenProducto extends Model
{
    protected $table = 'imagenes_producto';

    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'variante_id',
        'url',
        'es_portada',
        'orden'
    ];
    
    // RELACIONES CON PRODUCTO Y VARIANTE
    public function producto()
    {
        return $this->belongsTo(Productos::class, 'producto_id');
    }
    //  RELACIÓN CON VARIANTE DE PRODUCTO
    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_id');
    }
}