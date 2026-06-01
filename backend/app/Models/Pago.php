<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

// PAGO
class Pago extends Model
{
    protected $table = 'pagos';

    public $timestamps = false;

    protected $fillable = [
        'orden_id',
        'metodo',
        'referencia_pasarela',
        'estado',
        'monto',
        'pagado_en'
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }
}