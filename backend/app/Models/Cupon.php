<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

// CUPONES
class Cupon extends Model
{
    protected $table = 'cupones';

    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'tipo',
        'valor',
        'monto_minimo_pedido',
        'limite_usos',
        'usos_actuales',
        'activo',
        'expira_en'
    ];
}