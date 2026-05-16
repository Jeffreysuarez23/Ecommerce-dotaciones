<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

// LONA TALLAS
class LonaTalla extends Model
{
    protected $table = 'lona_tallas';

    public $timestamps = false;

    protected $fillable = [
        'lona_id',
        'talla',
        'cantidad'
    ];
}