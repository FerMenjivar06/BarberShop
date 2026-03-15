<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ServicioController extends Controller
{
    public function index()
    {
        try {
            $servicios = Servicio::orderBy('id', 'desc')->get();
            return response()->json($servicios, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener servicios', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:80|unique:servicios,nombre',
                'precio' => 'required|numeric|min:0',
            ]);

            $servicio = Servicio::create($request->all());
            return response()->json([
                'message' => 'Servicio registrado correctamente', 
                'servicio' => $servicio
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al guardar'], 500);
        }
    }

    public function show($id)
    {
        try {
            return response()->json(Servicio::findOrFail($id), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $servicio = Servicio::findOrFail($id);

            $request->validate([
                'nombre' => [
                    'required',
                    'string',
                    'max:80',
                    Rule::unique('servicios', 'nombre')->ignore($id)
                ],
                'precio' => 'required|numeric|min:0',
            ]);

            $servicio->update($request->all());
            return response()->json([
                'message' => 'Servicio actualizado correctamente', 
                'servicio' => $servicio
            ], 202);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $servicio = Servicio::with('citas')->findOrFail($id);

            if ($servicio->citas()->exists()) {
                return response()->json([
                    'message' => 'No se puede eliminar porque tiene citas agendadas.'
                ], 409);
            }

            $servicio->delete();
            return response()->json(['message' => 'Servicio eliminado correctamente'], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }
    }
}