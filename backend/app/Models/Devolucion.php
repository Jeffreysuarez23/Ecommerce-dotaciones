<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'devoluciones';

    public $timestamps = false;

    protected $fillable = [
        'orden_id',
        'motivo',
        'estado',
        'resolucion_admin',
        'resuelto_por'
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }
}