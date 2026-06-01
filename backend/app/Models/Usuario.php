<?php
namespace App\Models;
use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 

// MODELO DE USUARIOS, CON CAMPOS BÁSICOS PARA AUTENTICACIÓN Y ROLES. SE UTILIZA PARA GESTIONAR LOS USUARIOS DEL SISTEMA, INCLUYENDO ADMINISTRADORES Y CLIENTES.
class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; 

    protected $table = 'usuarios'; 
    public $timestamps = false; 

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'telefono',
        'rol'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verificado_en' => 'datetime', 
            'password' => 'hashed',
        ];
    }
}
