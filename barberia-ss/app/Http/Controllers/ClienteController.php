<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ClienteController extends Controller
{
    public function index()
    {
        try {
            $clientes = Cliente::orderBy('nombre', 'asc')->get();
            return response()->json($clientes, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener la lista de clientes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:80',
                'apellido' => 'required|string|max:80',
                'telefono' => 'required|string|max:20',
                'correo' => 'required|email|unique:clientes,correo'
            ]);

            $cliente = Cliente::create($request->all());

            return response()->json([
                'message' => 'Cliente registrado correctamente',
                'cliente' => $cliente
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            return response()->json($cliente, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Cliente no encontrado con ID = ' . $id
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $cliente = Cliente::findOrFail($id);

            $request->validate([
                'nombre' => 'required|string|max:80',
                'apellido' => 'required|string|max:80',
                'telefono' => 'required|string|max:20',
                'correo' => [
                    'required',
                    'email',
                    Rule::unique('clientes', 'correo')->ignore($id)
                ]
            ]);

            $cliente->update($request->all());

            return response()->json([
                'message' => 'Datos del cliente actualizados',
                'cliente' => $cliente
            ], 202);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $cliente = Cliente::with('citas')->findOrFail($id);

            if ($cliente->citas()->exists()) {
                return response()->json([
                    'message' => 'No se puede eliminar el cliente porque tiene citas registradas.'
                ], 409);
            }

            $cliente->delete();

            return response()->json([
                'message' => 'Cliente eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }
    }
}