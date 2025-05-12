<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function visits(Request $request)
    {
        $request->validate([
            'period.type' => 'required|string|in:month,quarter,year',
            'period.start' => 'required|date',
            'period.end' => 'required|date',
            'filters' => 'nullable|array'
        ]);
        
        $startDate = Carbon::parse($request->period['start']);
        $endDate = Carbon::parse($request->period['end']);
        $filters = $request->filters ?? [];
        
        $query = Visit::whereBetween('envisagee', [$startDate, $endDate]);
        
        // Application des filtres
        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }
        
        if (isset($filters['client_id'])) {
            $query->whereHas('employee', function($q) use ($filters) {
                $q->where('client_id', $filters['client_id']);
            });
        }
        
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        // Statistiques par type de visite
        $statsByType = $query->select('type')
                            ->selectRaw('COUNT(*) as total')
                            ->selectRaw('SUM(CASE WHEN etat = "Effectuée" THEN 1 ELSE 0 END) as completed')
                            ->selectRaw('SUM(CASE WHEN etat = "Programmée" THEN 1 ELSE 0 END) as scheduled')
                            ->selectRaw('SUM(CASE WHEN etat = "Annulée" THEN 1 ELSE 0 END) as cancelled')
                            ->groupBy('type')
                            ->get();
        
        // Statistiques globales
        $totalVisits = $query->count();
        $completedVisits = $query->where('etat', 'Effectuée')->count();
        $completionRate = $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100) : 0;
        
        return response()->json([
            'period' => [
                'type' => $request->period['type'],
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
                'label' => $this->getPeriodLabel($request->period['type'], $startDate)
            ],
            'total_visits' => $totalVisits,
            'completed_visits' => $completedVisits,
            'completion_rate' => $completionRate,
            'by_type' => $statsByType
        ]);
    }
    
    public function appointments(Request $request)
    {
        $request->validate([
            'period.type' => 'required|string|in:month,quarter,year',
            'period.start' => 'required|date',
            'period.end' => 'required|date',
            'filters' => 'nullable|array'
        ]);
        
        $startDate = Carbon::parse($request->period['start']);
        $endDate = Carbon::parse($request->period['end']);
        $filters = $request->filters ?? [];
        
        $query = Appointment::whereBetween('date', [$startDate, $endDate]);
        
        // Application des filtres
        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }
        
        if (isset($filters['client_id'])) {
            $query->whereHas('employee', function($q) use ($filters) {
                $q->where('client_id', $filters['client_id']);
            });
        }
        
        // Statistiques globales
        $totalAppointments = $query->count();
        $honoredAppointments = $query->where('honore', true)->count();
        $cancelledAppointments = $query->where('reporte', true)->count();
        $noResponseAppointments = $query->where('accepte', false)->count();
        
        return response()->json([
            'period' => [
                'type' => $request->period['type'],
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
                'label' => $this->getPeriodLabel($request->period['type'], $startDate)
            ],
            'total_appointments' => $totalAppointments,
            'honored_appointments' => $honoredAppointments,
            'cancelled_appointments' => $cancelledAppointments,
            'no_response_appointments' => $noResponseAppointments,
            'completion_rate' => $totalAppointments > 0 ? round(($honoredAppointments / $totalAppointments) * 100) : 0
        ]);
    }
    
    public function dashboard(Request $request)
    {
        $request->validate([
            'period.type' => 'required|string|in:month,quarter,year',
            'period.start' => 'required|date',
            'period.end' => 'required|date',
            'filters' => 'nullable|array'
        ]);
        
        $startDate = Carbon::parse($request->period['start']);
        $endDate = Carbon::parse($request->period['end']);
        
        // Statistiques globales
        $totalClients = Client::count();
        $totalEmployees = Employee::count();
        $totalVisits = Visit::whereBetween('envisagee', [$startDate, $endDate])->count();
        $completedVisits = Visit::whereBetween('envisagee', [$startDate, $endDate])
                                ->where('etat', 'Effectuée')
                                ->count();
        
        // Top 5 clients par nombre de visites
        $topClients = Client::withCount(['employees as visits_count' => function($query) use ($startDate, $endDate) {
                            $query->whereHas('visits', function($q) use ($startDate, $endDate) {
                                $q->whereBetween('envisagee', [$startDate, $endDate]);
                            });
                        }])
                        ->orderBy('visits_count', 'desc')
                        ->take(5)
                        ->get(['id', 'name', 'visits_count']);
        
        return response()->json([
            'period' => [
                'type' => $request->period['type'],
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
                'label' => $this->getPeriodLabel($request->period['type'], $startDate)
            ],
            'total_clients' => $totalClients,
            'total_employees' => $totalEmployees,
            'total_visits' => $totalVisits,
            'completed_visits' => $completedVisits,
            'completion_rate' => $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100) : 0,
            'top_clients' => $topClients
        ]);
    }
    
    private function getPeriodLabel($type, Carbon $date)
    {
        $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        
        switch ($type) {
            case 'month':
                return $months[$date->month - 1] . ' ' . $date->year;
            case 'quarter':
                $quarter = ceil($date->month / 3);
                return 'T' . $quarter . ' ' . $date->year;
            case 'year':
                return (string) $date->year;
            default:
                return '';
        }
    }
}