<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;


// ENVIO
class Envio extends Model
{
    protected $table = 'envios';

    public $timestamps = false;

    protected $fillable = [
        'orden_id',
        'transportadora',
        'guia',
        'estado',
        'fecha_entrega_estimada',
        'entregado_en'
    ];

    // RELACIONES

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }
}