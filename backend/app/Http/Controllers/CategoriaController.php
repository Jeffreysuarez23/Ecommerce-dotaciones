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
            if ($categoria->imagen_url) {
                // Supabase URLs are stored, we could parse the filename to delete from Supabase,
                // but for simplicity and safety, we'll just overwrite or leave it.
                // If we want to delete: Storage::disk('supabase')->delete($filename);
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Guardar el archivo en Supabase
            \Illuminate\Support\Facades\Storage::disk('supabase')->put($filename, file_get_contents($file));
            
            $categoria->imagen_url = \Illuminate\Support\Facades\Storage::disk('supabase')->url($filename);
            $categoria->save();

            return response()->json([
                'message' => 'Imagen subida exitosamente',
                'imagen_url' => $categoria->imagen_url
            ]);
        }

        return response()->json(['message' => 'No se recibió ninguna imagen'], 400);
    }
}