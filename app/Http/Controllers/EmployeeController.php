<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Visit;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%")
                  ->orWhere('poste', 'like', "%{$search}%");
            });
        }

        if ($request->has('departement')) {
            $query->where('departement', $request->departement);
        }

        $employees = $query->get();
        return response()->json($employees);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'matricule' => 'nullable|string|max:20',
            'gender' => 'nullable|string|max:10',
            'birthdate' => 'nullable|date',
            'contractType' => 'nullable|string|max:50',
            'startDate' => 'nullable|date',
            'spst' => 'nullable|string|max:100',
            'role' => 'nullable|string|max:100',
            'poste' => 'nullable|string|max:100',
            'departement' => 'nullable|string|max:100',
            'surveillance' => 'nullable|string|max:100',
            'pcsCode' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee = Employee::create($request->all());
        return response()->json($employee, 201);
    }

    public function show($id)
    {
        $employee = Employee::with(['visits', 'appointments'])->findOrFail($id);
        return response()->json($employee);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'client_id' => 'exists:clients,id',
            'name' => 'string|max:255',
            'matricule' => 'nullable|string|max:20',
            'gender' => 'nullable|string|max:10',
            'birthdate' => 'nullable|date',
            'contractType' => 'nullable|string|max:50',
            'startDate' => 'nullable|date',
            'spst' => 'nullable|string|max:100',
            'role' => 'nullable|string|max:100',
            'poste' => 'nullable|string|max:100',
            'departement' => 'nullable|string|max:100',
            'surveillance' => 'nullable|string|max:100',
            'pcsCode' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee->update($request->all());
        return response()->json($employee);
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
        return response()->json(null, 204);
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,xlsx',
            'client_id' => 'required|exists:clients,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $clientId = $request->client_id;

        // Handle file import (CSV or Excel)
        // For simplicity, let's assume we have a CSV file and parse it

        if ($file->getClientOriginalExtension() === 'csv') {
            $employees = $this->parseCSV($file, $clientId);
        } else {
            // Use a package like Laravel Excel to parse XLSX files
            $employees = $this->parseExcel($file, $clientId);
        }

        return response()->json([
            'success' => true,
            'imported' => count($employees),
            'employees' => $employees
        ]);
    }

    private function parseCSV($file, $clientId)
    {
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        // Assuming first row is headers
        $headers = array_shift($data);

        $employees = [];

        foreach ($data as $row) {
            $rowData = array_combine($headers, $row);

            // Create the employee
            $employee = new Employee([
                'client_id' => $clientId,
                'name' => $rowData['name'] ?? null,
                'matricule' => $rowData['matricule'] ?? null,
                'gender' => $rowData['gender'] ?? null,
                'birthdate' => $rowData['birthdate'] ?? null,
                'contractType' => $rowData['contractType'] ?? null,
                'startDate' => $rowData['startDate'] ?? null,
                'spst' => $rowData['spst'] ?? null,
                'role' => $rowData['role'] ?? null,
                'poste' => $rowData['poste'] ?? null,
                'departement' => $rowData['departement'] ?? null,
                'surveillance' => $rowData['surveillance'] ?? null,
                'pcsCode' => $rowData['pcsCode'] ?? null,
            ]);

            $employee->save();
            $employees[] = $employee;
        }

        return $employees;
    }

    private function parseExcel($file, $clientId)
    {
        // Implement Excel parsing using a package like Laravel Excel
        // This is just a placeholder
        return [];
    }

    public function export($format, Request $request)
    {
        $query = Employee::query();

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $employees = $query->get();

        // Generate file content based on format
        switch ($format) {
            case 'pdf':
                // PDF generation code (you may want to use a library like Dompdf)
                $pdf = app()->make('dompdf.wrapper');
                $pdf->loadView('exports.employees', ['employees' => $employees]);
                return $pdf->download('employees.pdf');

            case 'xlsx':
                // Excel generation (consider using Laravel Excel or similar)
                // For simplicity, we'll return a CSV in this example

            case 'csv':
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename=employees.csv',
                ];

                $callback = function() use ($employees) {
                    $handle = fopen('php://output', 'w');

                    // Header row
                    fputcsv($handle, ['ID', 'Name', 'Matricule', 'Role', 'Department', 'Start Date']);

                    // Data rows
                    foreach ($employees as $employee) {
                        fputcsv($handle, [
                            $employee->id,
                            $employee->name,
                            $employee->matricule,
                            $employee->role,
                            $employee->departement,
                            $employee->startDate
                        ]);
                    }

                    fclose($handle);
                };

                return Response::stream($callback, 200, $headers);

            default:
                return response()->json(['error' => 'Unsupported format'], 400);
        }
    }

    public function stats(Request $request)
    {
        $query = Employee::query();

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $totalEmployees = $query->count();

        // Department distribution
        $departmentCounts = $query->select('departement')
                                  ->selectRaw('count(*) as count')
                                  ->groupBy('departement')
                                  ->get()
                                  ->pluck('count', 'departement');

        // Get employees with upcoming visits
        $employeesWithUpcomingVisits = Visit::where('etat', 'Programmée')
                                           ->whereDate('envisagee', '>=', now())
                                           ->count();

        return response()->json([
            'total' => $totalEmployees,
            'departments' => $departmentCounts,
            'with_upcoming_visits' => $employeesWithUpcomingVisits
        ]);
    }
}