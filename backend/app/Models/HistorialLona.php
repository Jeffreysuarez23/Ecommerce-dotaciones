<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

// HISTORIAL DE LONA
class HistorialLona extends Model
{
    protected $table = 'historial_lonas';

    public $timestamps = false;

    protected $fillable = [
        'lona_id',
        'orden_item_id',
        'accion',
        'talla',
        'cantidad_cambio',
        'cantidad_restante',
        'snapshot_json',
        'notas',
        'creado_por',
        'creado_en'
    ];

    protected $casts = [
        'snapshot_json' => 'array'
    ];

    public function lona()
    {
        return $this->belongsTo(Lona::class);
    }
}