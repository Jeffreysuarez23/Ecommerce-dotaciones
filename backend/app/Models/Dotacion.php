<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

// MODELO DE DOTACIÓN, QUE REPRESENTA LOS CONJUNTOS DE LONAS ASOCIADOS A UNA DOTACIÓN ESPECÍFICA. INCLUYE CAMPOS PARA NOMBRE, DESCRIPCIÓN, CANTIDAD MÍNIMA Y MÁXIMA DE LONAS, LONAS ACTIVAS Y FECHA DE ALERTA.
class Dotacion extends Model
{
    protected $table = 'dotaciones';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'min_lonas',
        'max_lonas',
        'lonas_activas',
        'alerta_enviada_en'
    ];

    // RELACIÓN CON LONAS ASOCIADAS 
    public function lonas()
    {
        return $this->hasMany(Lona::class, 'dotacion_id');
    }
}