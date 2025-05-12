<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $query = Visit::with('employee');
        
        // Filtrage par employé
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        
        // Filtrage par client via la relation employee
        if ($request->has('client_id')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('client_id', $request->client_id);
            });
        }
        
        // Filtrage par type de visite
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        // Filtrage par état
        if ($request->has('etat')) {
            $query->where('etat', $request->etat);
        }
        
        // Filtrage par date
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('envisagee', [$request->date_from, $request->date_to]);
        }
        
        $visits = $query->get();
        
        return response()->json($visits);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|string|max:50',
            'etat' => 'required|string|max:50',
            'envisagee' => 'required|date',
            'effectuee' => 'nullable|date',
            'suivi' => 'nullable|string|max:50',
            'apte' => 'nullable|boolean',
            'observations' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $visit = Visit::create($request->all());
        return response()->json($visit, 201);
    }

    public function show($id)
    {
        $visit = Visit::with('employee')->findOrFail($id);
        return response()->json($visit);
    }

    public function update(Request $request, $id)
    {
        $visit = Visit::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'exists:employees,id',
            'type' => 'string|max:50',
            'etat' => 'string|max:50',
            'envisagee' => 'date',
            'effectuee' => 'nullable|date',
            'suivi' => 'nullable|string|max:50',
            'apte' => 'nullable|boolean',
            'observations' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $visit->update($request->all());
        return response()->json($visit);
    }

    public function destroy($id)
    {
        $visit = Visit::findOrFail($id);
        $visit->delete();
        return response()->json(null, 204);
    }
    
    public function export($format, Request $request)
    {
        $query = Visit::with('employee');
        
        if ($request->has('employee_ids') && is_array($request->employee_ids)) {
            $query->whereIn('employee_id', $request->employee_ids);
        }
        
        $visits = $query->get();
        
        // Generate file content based on format
        switch ($format) {
            case 'pdf':
                // PDF generation code (you may want to use a library like Dompdf)
                // In real app, implement this with an appropriate PDF library
                return response()->json([
                    'message' => 'PDF export initiated for ' . $visits->count() . ' visits'
                ]);

            case 'xlsx':
                // Excel generation (consider using Laravel Excel or similar)
                // In real app, implement this with an appropriate Excel library
                return response()->json([
                    'message' => 'Excel export initiated for ' . $visits->count() . ' visits'
                ]);

            case 'csv':
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename=visits.csv',
                ];

                $callback = function() use ($visits) {
                    $handle = fopen('php://output', 'w');

                    // Header row
                    fputcsv($handle, ['ID', 'Employee', 'Type', 'Status', 'Planned Date', 'Completed Date', 'Follow-up', 'Fit for Work']);

                    // Data rows
                    foreach ($visits as $visit) {
                        fputcsv($handle, [
                            $visit->id,
                            $visit->employee->name,
                            $visit->type,
                            $visit->etat,
                            $visit->envisagee,
                            $visit->effectuee,
                            $visit->suivi,
                            $visit->apte ? 'Yes' : 'No'
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