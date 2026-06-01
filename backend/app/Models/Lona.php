<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

// LONAS
class Lona extends Model
{
    protected $table = 'lonas';

    public $timestamps = false;

    public function dotacion()
    {
        return $this->belongsTo(Dotacion::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function tallas()
    {
        return $this->hasMany(LonaTalla::class, 'lona_id');
    }

    protected $fillable = [
        'dotacion_id',
        'codigo',
        'tipo_producto',
        'categoria_id',
        'estado',
        'activa',
        'capacidad_maxima'
    ];
}