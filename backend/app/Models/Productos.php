<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\VarianteProducto;

// MODELO DE PRODUCTOS
class Productos extends Model
{
    use SoftDeletes;

    const DELETED_AT = 'eliminado_en';

    protected $table = 'productos';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'precio_minorista',
        'precio_mayorista',
        'min_cantidad_mayorista',
        'publicado',
        'categoria_id',
        'destacado'
    ];
    
    // RELACIÓN CON VARIANTES DE PRODUCTO
    public function variantes()
    {
        return $this->hasMany(VarianteProducto::class, 'producto_id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenProducto::class, 'producto_id')
                    ->orderBy('orden')
                    ->orderBy('id');
    }
}
