<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// CONTROLADOR DE AUTENTICACIÓN PARA REGISTRAR NUEVOS USUARIOS, INICIAR SESIÓN Y OBTENER EL PERFIL DEL USUARIO AUTENTICADO. UTILIZA SANCTUM PARA LA GESTIÓN DE TOKENS DE AUTENTICACIÓN.
class AuthController extends Controller
{
    // REGISTRAR NUEVO USUARIO, CREANDO UN TOKEN DE AUTENTICACIÓN PARA USO POSTERIOR EN PETICIONES PROTEGIDAS.
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6',
            'telefono' => 'nullable|string|max:20'
        ]);

        $user = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'rol' => 'cliente'
        ]);

        // Enviar correo de verificación
        $this->sendVerificationEmail($user);

        return response()->json([
            'message' => 'Usuario registrado correctamente. Revisa tu correo para activar tu cuenta.',
            'user' => $user
        ]);
    }

    // INICIAR SESIÓN VERIFICANDO LAS CREDENCIALES Y GENERANDO
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string'
        ]);

        // Buscar por email o por nombre
        $user = Usuario::where('email', $request->login)
            ->orWhere('nombre', $request->login)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // Verificar si el email está activado
        if (!$user->email_verificado_en) {
            return response()->json([
                'message' => 'Tu correo electrónico no ha sido verificado. Revisa tu bandeja de entrada.',
                'email_not_verified' => true,
                'email' => $user->email
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => $user
        ]);
    }

    // VERIFICAR EMAIL DESDE EL ENLACE ENVIADO POR CORREO
    public function verifyEmail(Request $request, $id)
    {
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

        if (!$request->hasValidSignature()) {
            return redirect($frontendUrl . '/login?verified=expired');
        }

        $user = Usuario::findOrFail($id);

        if (!$user->email_verificado_en) {
            $user->email_verificado_en = now();
            $user->save();
        }

        return redirect($frontendUrl . '/login?verified=1');
    }

    // REENVIAR CORREO DE VERIFICACIÓN
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = Usuario::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'No se encontró una cuenta con ese correo.'
            ], 404);
        }

        if ($user->email_verificado_en) {
            return response()->json([
                'message' => 'Este correo ya fue verificado.'
            ]);
        }

        $this->sendVerificationEmail($user);

        return response()->json([
            'message' => 'Correo de verificación reenviado exitosamente.'
        ]);
    }
    
    // OBTENER EL PERFIL DEL USUARIO AUTENTICADO UTILIZANDO EL
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
   
    // ACTUALIZAR PERFIL DEL USUARIO AUTENTICADO
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'nombre' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|unique:usuarios,email,' . $user->id,
            'telefono' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed'
        ]);

        if ($request->has('nombre')) {
            $user->nombre = $request->nombre;
        }

        if ($request->has('email') && $request->email !== $user->email) {
            $user->email = $request->email;
            $user->email_verificado_en = null; // Requiere nueva verificación
        }

        if ($request->has('telefono')) {
            $user->telefono = $request->telefono;
        }

        if ($request->has('password') && !empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Enviar correo si cambió el email
        if ($request->has('email') && $request->email !== $user->getOriginal('email')) {
            $this->sendVerificationEmail($user);
            return response()->json([
                'message' => 'Perfil actualizado. Revisa tu nuevo correo para verificarlo.',
                'user' => $user
            ]);
        }

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user' => $user
        ]);
    }

    // ELIMINAR CUENTA DEL USUARIO
    public function deleteProfile(Request $request)
    {
        $user = $request->user();
        
        // Revoke all tokens
        $user->tokens()->delete();
        
        // Soft delete (o delete físico según modelo, asumo que eliminamos para el cliente)
        $user->delete();

        return response()->json([
            'message' => 'Cuenta eliminada correctamente'
        ]);
    }

    // CERRAR SESIÓN ELIMINANDO EL TOKEN DE AUTENTICACIÓN ACTUAL
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout exitoso'
        ]);
    }

    // SOLICITAR RECUPERACIÓN DE CONTRASEÑA
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:usuarios,email']);

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        $user = Usuario::where('email', $request->email)->first();
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        $resetUrl = $frontendUrl . "/reset-password?token=" . $token . "&email=" . urlencode($request->email);

        $html = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Recuperación de contraseña</title>
        </head>
        <body style="margin:0;padding:0;background-color:#f5f3ef;font-family:\'Segoe UI\',\'Helvetica Neue\',Arial,sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f3ef;padding:40px 0;">
                <tr>
                    <td align="center">
                        <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
                            <tr>
                                <td style="background:linear-gradient(135deg,#1a1a1a 0%,#2d2d2d 100%);padding:40px 40px 32px;text-align:center;">
                                    <h1 style="margin:0;font-size:28px;font-weight:700;color:#ffffff;letter-spacing:-0.5px;">
                                        Recuperación de contraseña
                                    </h1>
                                    <p style="margin:8px 0 0;font-size:14px;color:rgba(255,255,255,0.65);">
                                        Hola, ' . htmlspecialchars($user->nombre) . '
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:40px 40px 0;text-align:center;">
                                    <p style="margin:0;font-size:16px;color:#1a1a1a;line-height:1.6;">
                                        Recibimos una solicitud para restablecer tu contraseña. Haz clic en el botón de abajo para cambiarla:
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:32px 40px;text-align:center;">
                                    <a href="' . $resetUrl . '" style="display:inline-block;background-color:#1a1a1a;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:8px;font-size:14px;font-weight:600;letter-spacing:0.02em;">
                                        Restablecer Contraseña →
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:0 40px 32px;text-align:center;">
                                    <p style="margin:0;font-size:14px;color:#666;">
                                        Si no solicitaste este cambio, ignora este correo.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color:#faf8f4;padding:24px 40px;border-top:1px solid #ede8de;text-align:center;">
                                    <p style="margin:0;font-size:12px;color:#aaa;">
                                        Este correo fue enviado automáticamente. Si tienes alguna duda, contáctanos.
                                    </p>
                                    <p style="margin:8px 0 0;font-size:12px;color:#ccc;">
                                        © ' . date('Y') . ' Dgala Ecommerce. Todos los derechos reservados.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';

        Mail::html($html, function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Restablece tu contraseña - Dgala Ecommerce');
        });

        return response()->json(['message' => 'Enlace de recuperación enviado al correo.']);
    }

    // RESTABLECER CONTRASEÑA
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return response()->json(['message' => 'Token inválido o expirado.'], 400);
        }

        Usuario::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Contraseña actualizada exitosamente.']);
    }

    // MÉTODO PRIVADO PARA ENVIAR EL CORREO DE VERIFICACIÓN
    private function sendVerificationEmail(Usuario $user)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            ['id' => $user->id]
        );

        $html = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Verificación de cuenta</title>
        </head>
        <body style="margin:0;padding:0;background-color:#f5f3ef;font-family:\'Segoe UI\',\'Helvetica Neue\',Arial,sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f3ef;padding:40px 0;">
                <tr>
                    <td align="center">
                        <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
                            <tr>
                                <td style="background:linear-gradient(135deg,#1a1a1a 0%,#2d2d2d 100%);padding:40px 40px 32px;text-align:center;">
                                    <h1 style="margin:0;font-size:28px;font-weight:700;color:#ffffff;letter-spacing:-0.5px;">
                                        Verificación de cuenta
                                    </h1>
                                    <p style="margin:8px 0 0;font-size:14px;color:rgba(255,255,255,0.65);">
                                        ¡Hola, ' . htmlspecialchars($user->nombre) . '!
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:40px 40px 0;text-align:center;">
                                    <p style="margin:0;font-size:16px;color:#1a1a1a;line-height:1.6;">
                                        Gracias por registrarte. Para activar tu cuenta, haz clic en el siguiente botón:
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:32px 40px;text-align:center;">
                                    <a href="' . $verificationUrl . '" style="display:inline-block;background-color:#1a1a1a;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:8px;font-size:14px;font-weight:600;letter-spacing:0.02em;">
                                        Verificar mi correo →
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:0 40px 32px;text-align:center;">
                                    <p style="margin:0;font-size:14px;color:#666;">
                                        Si no creaste esta cuenta, puedes ignorar este correo.<br>
                                        <span style="font-size:12px;color:#aaa;display:block;margin-top:8px;">Este enlace expira en 24 horas.</span>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color:#faf8f4;padding:24px 40px;border-top:1px solid #ede8de;text-align:center;">
                                    <p style="margin:0;font-size:12px;color:#aaa;">
                                        Este correo fue enviado automáticamente. Si tienes alguna duda, contáctanos.
                                    </p>
                                    <p style="margin:8px 0 0;font-size:12px;color:#ccc;">
                                        © ' . date('Y') . ' Dgala Ecommerce. Todos los derechos reservados.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';

        Mail::html($html, function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Verifica tu cuenta - Ecommerce Dotaciones');
        });
    }
}