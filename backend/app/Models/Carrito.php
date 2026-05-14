<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

// CARRITO 
class Carrito extends Model
{
    protected $table = 'carritos';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'session_id',
        'cupon_id'
    ];

    public function items()
    {
        return $this->hasMany(CarritoItem::class, 'carrito_id');
    }
}