<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

// CATEGORIA
class Categoria extends Model
{
    protected $table = 'categorias';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'slug',
        'padre_id',
        'orden',
        'destacada',
        'imagen_url'
    ];

    
    public function padre()
    {
        return $this->belongsTo(Categoria::class, 'padre_id');
    }

  
    public function hijos()
    {
        return $this->hasMany(Categoria::class, 'padre_id')->with('hijos');
    }
}