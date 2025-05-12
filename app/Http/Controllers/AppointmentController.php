<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with('employee');
        
        // Filtrage par employé
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        
        // Filtrage par date
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('date', [$request->date_from, $request->date_to]);
        }
        
        // Filtrage par statut
        if ($request->has('honore')) {
            $query->where('honore', $request->honore === 'true');
        }
        
        $appointments = $query->get();
        
        return response()->json($appointments);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'envoi' => 'nullable|date',
            'ar' => 'nullable|boolean',
            'ordonnance' => 'nullable|boolean',
            'accepte' => 'nullable|boolean',
            'excusable' => 'nullable|boolean',
            'reporte' => 'nullable|boolean',
            'honore' => 'nullable|boolean',
            'motif' => 'nullable|string|max:255',
            'commentaire' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $appointment = Appointment::create($request->all());
        return response()->json($appointment, 201);
    }

    public function show($id)
    {
        $appointment = Appointment::with('employee')->findOrFail($id);
        return response()->json($appointment);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'exists:employees,id',
            'date' => 'date',
            'envoi' => 'nullable|date',
            'ar' => 'nullable|boolean',
            'ordonnance' => 'nullable|boolean',
            'accepte' => 'nullable|boolean',
            'excusable' => 'nullable|boolean',
            'reporte' => 'nullable|boolean',
            'honore' => 'nullable|boolean',
            'motif' => 'nullable|string|max:255',
            'commentaire' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $appointment->update($request->all());
        return response()->json($appointment);
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        return response()->json(null, 204);
    }
    
    public function export($format, Request $request)
    {
        $query = Appointment::with('employee');
        
        if ($request->has('employee_ids') && is_array($request->employee_ids)) {
            $query->whereIn('employee_id', $request->employee_ids);
        }
        
        $appointments = $query->get();
        
        // Generate file content based on format
        switch ($format) {
            case 'pdf':
                // In real app, implement this with an appropriate PDF library
                return response()->json([
                    'message' => 'PDF export initiated for ' . $appointments->count() . ' appointments'
                ]);

            case 'xlsx':
                // In real app, implement this with an appropriate Excel library
                return response()->json([
                    'message' => 'Excel export initiated for ' . $appointments->count() . ' appointments'
                ]);

            case 'csv':
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename=appointments.csv',
                ];

                $callback = function() use ($appointments) {
                    $handle = fopen('php://output', 'w');

                    // Header row
                    fputcsv($handle, ['ID', 'Employee', 'Date', 'Sending Date', 'Accepted', 'Honored', 'Postponed', 'Reason']);

                    // Data rows
                    foreach ($appointments as $appointment) {
                        fputcsv($handle, [
                            $appointment->id,
                            $appointment->employee->name,
                            $appointment->date,
                            $appointment->envoi,
                            $appointment->accepte ? 'Yes' : 'No',
                            $appointment->honore ? 'Yes' : 'No',
                            $appointment->reporte ? 'Yes' : 'No',
                            $appointment->motif
                        ]);
                    }

                    fclose($handle);
                };

                return Response::stream($callback, 200, $headers);

            default:
                return response()->json(['error' => 'Unsupported format'], 400);
        }
    }
}