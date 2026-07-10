<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrabajadorController extends Controller
{
    public function index()
    {
        return response()->json(Trabajador::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'required|string|unique:trabajadores',
            'cargo' => 'required|string|max:255',
            'fecha_ingreso' => 'required|date',
            'hoja_vida' => 'required|file|mimes:pdf|max:5120' // Max 5MB PDF
        ]);

        $data = $request->except('hoja_vida');
        
        if ($request->hasFile('hoja_vida')) {
            $path = $request->file('hoja_vida')->store('hojas_vida', 'public');
            $data['hoja_vida_path'] = $path;
        }

        $trabajador = Trabajador::create($data);

        return response()->json(['message' => 'Trabajador creado exitosamente', 'trabajador' => $trabajador], 201);
    }

    public function update(Request $request, $id)
    {
        $trabajador = Trabajador::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'required|string|unique:trabajadores,documento,' . $trabajador->id,
            'cargo' => 'required|string|max:255',
            'fecha_ingreso' => 'required|date',
            'hoja_vida' => 'nullable|file|mimes:pdf|max:5120'
        ]);

        $data = $request->except('hoja_vida');

        if ($request->hasFile('hoja_vida')) {
            // Eliminar la anterior si existe
            if ($trabajador->hoja_vida_path) {
                Storage::disk('public')->delete($trabajador->hoja_vida_path);
            }
            $path = $request->file('hoja_vida')->store('hojas_vida', 'public');
            $data['hoja_vida_path'] = $path;
        }

        $trabajador->update($data);

        return response()->json(['message' => 'Trabajador actualizado exitosamente', 'trabajador' => $trabajador]);
    }

    public function destroy($id)
    {
        $trabajador = Trabajador::findOrFail($id);
        if ($trabajador->hoja_vida_path) {
            Storage::disk('public')->delete($trabajador->hoja_vida_path);
        }
        $trabajador->delete();

        return response()->json(['message' => 'Trabajador eliminado exitosamente']);
    }
}
