<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class UsuarioController extends Controller
{
    // Obtener todos los usuarios
    public function index()
    {
        $usuarios = Usuario::all();
        return response()->json($usuarios);
    }

    // Actualizar el rol de un usuario
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'rol' => 'required|in:cliente,admin,super_admin'
        ]);

        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $usuario->rol = $request->rol;
        $usuario->save();

        return response()->json([
            'message' => 'Rol actualizado correctamente',
            'usuario' => $usuario
        ]);
    }

    // Crear un nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email:rfc,dns|unique:usuarios,email',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'rol' => 'required|in:cliente,admin,super_admin',
            'estado' => 'required|boolean'
        ]);

        $usuario = new Usuario();
        $usuario->nombre = $request->nombre;
        $usuario->email = $request->email;
        $usuario->telefono = $request->telefono;
        $usuario->password = bcrypt($request->password);
        $usuario->rol = $request->rol;
        $usuario->email_verificado_en = $request->estado ? now() : null;
        $usuario->save();

        if (!$request->estado) {
            try {
                $this->sendVerificationEmail($usuario);
            } catch (\Exception $e) {
                // Si falla el envío (ej: el servidor SMTP rechaza el correo porque no existe)
                $usuario->delete();
                return response()->json([
                    'message' => 'No se pudo enviar el correo. Es posible que la dirección de correo no exista o sea inválida.'
                ], 400);
            }
        }

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'usuario' => $usuario
        ], 201);
    }

    // Actualizar datos del usuario
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email,' . $id,
            'telefono' => 'nullable|string|max:20',
            'rol' => 'required|in:cliente,admin,super_admin',
            'estado' => 'required|boolean'
        ]);

        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $usuario->nombre = $request->nombre;
        $usuario->email = $request->email;
        $usuario->telefono = $request->telefono;
        $usuario->rol = $request->rol;
        $usuario->email_verificado_en = $request->estado ? now() : null;
        $usuario->save();

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'usuario' => $usuario
        ]);
    }

    // Eliminar un usuario
    public function destroy($id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    private function sendVerificationEmail(Usuario $user)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            ['id' => $user->id]
        );

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 40px 0; }
                .card { max-width: 480px; margin: 0 auto; background: white; border-radius: 16px; padding: 48px 40px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
                .logo { text-align: center; font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px; }
                .subtitle { text-align: center; font-size: 14px; color: #999; margin-bottom: 32px; }
                h2 { font-size: 22px; color: #1a1a1a; margin: 0 0 16px 0; }
                p { font-size: 15px; color: #555; line-height: 1.6; margin: 0 0 24px 0; }
                .btn { display: inline-block; padding: 16px 40px; background: #1a1a1a; color: white !important; text-decoration: none; border-radius: 100px; font-size: 15px; font-weight: 600; }
                .btn-wrap { text-align: center; margin: 32px 0; }
                .footer { text-align: center; font-size: 12px; color: #bbb; margin-top: 32px; }
            </style>
        </head>
        <body>
            <div class="card">
                <p class="logo">Ecommerce Dotaciones</p>
                <p class="subtitle">Activación de cuenta</p>
                <h2>¡Hola, ' . htmlspecialchars($user->nombre) . '!</h2>
                <p>Un administrador ha creado una cuenta para ti. Para activarla, haz clic en el siguiente botón:</p>
                <div class="btn-wrap">
                    <a href="' . $verificationUrl . '" class="btn">Activar mi cuenta</a>
                </div>
                <p>Si no esperabas esto, puedes ignorar este correo.</p>
                <p class="footer">Este enlace expira en 24 horas.</p>
            </div>
        </body>
        </html>';

        Mail::html($html, function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Activa tu cuenta - Ecommerce Dotaciones');
        });
    }
}
