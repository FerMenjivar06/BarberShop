<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class CitaController extends Controller
{
    public function index()
    {
        try {
            $citas = Cita::with(['cliente', 'barbero.usuario', 'servicio'])
                ->orderBy('fecha', 'desc')
                ->get();
            return response()->json($citas, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al listar citas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction(); 
        try {
            $request->validate([
                'fecha' => 'required|date',
                'hora' => 'required',
                'cliente_id' => 'required|exists:clientes,id',
                'barbero_id' => 'required|exists:barberos,id',
                'servicio_id' => 'required|exists:servicios,id',
                'estado' => 'nullable|string'
            ]);

            $cita = Cita::create($request->all());
            
            DB::commit(); // Confirmamos los cambios
            return response()->json([
                'message' => 'Cita agendada correctamente', 
                'cita' => $cita->load(['cliente', 'barbero.usuario', 'servicio'])
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack(); // Deshacemos si algo falla
            return response()->json([
                'message' => 'Error interno al agendar', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $cita = Cita::with(['cliente', 'barbero.usuario', 'servicio'])->findOrFail($id);
            return response()->json($cita, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Cita no encontrada con ID = ' . $id
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $cita = Cita::findOrFail($id);
            
            $request->validate([
                'fecha' => 'date',
                'cliente_id' => 'exists:clientes,id',
                'barbero_id' => 'exists:barberos,id',
                'servicio_id' => 'exists:servicios,id',
                'estado' => 'string'
            ]);

            $cita->update($request->all());
            return response()->json([
                'message' => 'Cita actualizada',
                'cita' => $cita
            ], 202);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'No se encontró la cita'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $cita = Cita::findOrFail($id);
            $cita->delete();
            return response()->json(['message' => 'Cita eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'La cita no existe'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar'], 500);
        }
    }
}