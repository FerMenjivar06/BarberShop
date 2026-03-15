<?php

namespace App\Http\Controllers;

use App\Models\Barbero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class BarberoController extends Controller
{
    public function index()
    {
        try {
            $barberos = Barbero::with('usuario')->orderBy('id', 'desc')->get();
            return response()->json($barberos, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener la lista de barberos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'usuario_id' => 'required|exists:users,id|unique:barberos,usuario_id',
                'especialidad' => 'nullable|string|max:100',
            ], [
                'usuario_id.unique' => 'Este usuario ya tiene el rol de barbero asignado.'
            ]);

            $barbero = Barbero::create($request->all());

            return response()->json([
                'message' => 'Barbero asignado correctamente',
                'barbero' => $barbero->load('usuario')
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al guardar'], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $barbero = Barbero::with('usuario')->findOrFail($id);
            return response()->json($barbero, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Barbero no encontrado'], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $barbero = Barbero::findOrFail($id);
            
            $request->validate([
                'especialidad' => 'required|string|max:100',
            ]);

            $barbero->update($request->all());

            return response()->json([
                'message' => 'Información del barbero actualizada',
                'barbero' => $barbero->load('usuario')
            ], 202);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar'], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $barbero = Barbero::with('citas')->findOrFail($id);

            if ($barbero->citas()->exists()) {
                return response()->json([
                    'message' => 'No se puede eliminar: el barbero tiene citas en su agenda.'
                ], 409);
            }

            $barbero->delete();

            return response()->json([
                'message' => 'Barbero eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Barbero no encontrado'], 404);
        }
    }
}