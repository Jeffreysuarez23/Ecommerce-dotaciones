<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

// MODELO ORDEN 
class Orden extends Model
{
    protected $table = 'ordenes';


    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'direccion_id',
        'cupon_id',
        'numero',
        'estado',
        'tipo_precio',
        'subtotal',
        'descuento',
        'envio_costo',
        'total',
        'notas_cliente'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'envio_costo' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // RELACIONES USUARIO
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // RELACION DIRECCIÓN
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'direccion_id');
    }

    // RELACION CUPÓN
    public function cupon()
    {
        return $this->belongsTo(Cupon::class, 'cupon_id');
    }

    // RELACIÓN ITEMS
    public function items()
    {
        return $this->hasMany(OrdenItem::class, 'orden_id');
    }

    // RELACIÓN PAGO
    public function pago()
    {
        return $this->hasMany(Pago::class, 'orden_id');
    }

    // RELACIÓN ENVÍO
    public function envio()
    {
        return $this->hasOne(Envio::class, 'orden_id');
    }

    // RELACIÓN DEVOLUCIÓN
    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class);
    }
}