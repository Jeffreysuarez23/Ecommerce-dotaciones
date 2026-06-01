<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

// MODELO PARA LA TABLA DE NOTIFICACIONES
class Notificacion extends Model
{
    protected $table = 'notificaciones';

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    protected $fillable = [
        'usuario_id',
        'tipo',
        'titulo',
        'mensaje',
        'leido_en',
        'confirmado_por'
    ];
}