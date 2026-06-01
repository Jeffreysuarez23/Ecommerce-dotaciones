<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Support\Str;

// CONTROLADOR PARA GESTIONAR LAS CATEGORÍAS DE PRODUCTOS. PERMITE CREAR, LISTAR, ACTUALIZAR Y ELIMINAR CATEGORÍAS. LAS CATEGORÍAS PUEDEN SER PADRE O HIJO (SUBCATEGORÍA) Y SE ORDENAN POR UN CAMPO "ORDEN".
class CategoriaController extends Controller
{
    // LISTAR 
    public function index()
    {
        $categorias = Categoria::whereNull('padre_id')
            ->with('hijos')
            ->orderBy('orden')
            ->get();

        return response()->json($categorias);
    }

    //  CREAR
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'padre_id' => 'nullable|integer|exists:categorias,id',
            'orden' => 'nullable|integer|min:0',
            'destacada' => 'nullable|boolean'
        ]);

        $validated['slug'] = Str::slug($validated['nombre']);
        $validated['orden'] = $validated['orden'] ?? 0;
        $validated['destacada'] = $validated['destacada'] ? 1 : 0;

        $categoria = Categoria::create($validated);

        return response()->json([
            'message' => 'Categoría creada',
            'data' => $categoria
        ], 201);
    }

    //  VER UNA
    public function show($id)
    {
        $categoria = Categoria::with('hijos')->findOrFail($id);

        return response()->json($categoria);
    }

    //  ACTUALIZAR
    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'padre_id' => 'nullable|integer|exists:categorias,id',
            'orden' => 'nullable|integer|min:0',
            'destacada' => 'nullable|boolean'
        ]);

        if (isset($validated['nombre'])) {
            $validated['slug'] = Str::slug($validated['nombre']);
        }
        
        if (isset($validated['destacada'])) {
            $validated['destacada'] = $validated['destacada'] ? 1 : 0;
        }

        $categoria->update($validated);

        return response()->json([
            'message' => 'Categoría actualizada',
            'data' => $categoria
        ]);
    }

    //  ELIMINAR
    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);

        //  Validar que no tenga hijos
        if ($categoria->hijos()->count() > 0) {
            return response()->json([
                'message' => 'No puedes eliminar una categoría con subcategorías'
            ], 400);
        }

        $categoria->delete();

        return response()->json([
            'message' => 'Categoría eliminada'
        ]);
    }

    //  SUBIR IMAGEN
    public function uploadImagen(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096'
        ]);

        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($categoria->imagen_url) {
                $oldPath = str_replace(url('/'), public_path(), $categoria->imagen_url);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/categorias'), $filename);
            
            $categoria->imagen_url = url('images/categorias/' . $filename);
            $categoria->save();

            return response()->json([
                'message' => 'Imagen subida exitosamente',
                'imagen_url' => $categoria->imagen_url
            ]);
        }

        return response()->json(['message' => 'No se recibió ninguna imagen'], 400);
    }
}