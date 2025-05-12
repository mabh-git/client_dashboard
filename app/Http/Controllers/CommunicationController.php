<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Http\Request;

class CommunicationController extends Controller
{
    public function contactManager(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);
        
        $employee = Employee::with('client')->findOrFail($request->employee_id);
        $client = $employee->client;
        
        // Dans un cas réel, on enverrait un email ou une notification
        // au manager du client
        
        return response()->json([
            'success' => true,
            'message' => 'Message envoyé au manager avec succès',
            'recipient' => $client->contact_person ?? 'Manager',
            'recipient_email' => $client->contact_email ?? 'contact@' . ($client->name ?? 'client.com')
        ]);
    }
    
    public function contactAssistant(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);
        
        $employee = Employee::findOrFail($request->employee_id);
        
        // Dans un cas réel, on enverrait un email ou une notification
        // à l'assistant SPST
        
        return response()->json([
            'success' => true,
            'message' => 'Message envoyé à l\'assistant avec succès',
            'recipient' => 'Assistant SPST',
            'recipient_email' => 'assistant.spst@example.com'
        ]);
    }
    
    public function contactRRH(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);
        
        $client = Client::findOrFail($request->client_id);
        
        // Dans un cas réel, on enverrait un email ou une notification
        // au RRH du client
        
        return response()->json([
            'success' => true,
            'message' => 'Message envoyé au RRH du client avec succès',
            'recipient' => 'RRH ' . $client->name,
            'recipient_email' => 'rrh@' . ($client->website ?? 'client.com')
        ]);
    }
    
    public function declareIncident(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'type' => 'required|string|max:50',
            'severity' => 'required|string|in:low,medium,high,critical',
            'date' => 'required|date',
            'description' => 'required|string'
        ]);
        
        $client = Client::findOrFail($request->client_id);
        
        // Dans un cas réel, on enregistrerait l'incident en base de données
        // et on enverrait des notifications aux personnes concernées
        
        // Générer un identifiant unique pour l'incident
        $incidentId = 'INC-' . date('Ymd') . '-' . rand(1000, 9999);
        
        return response()->json([
            'success' => true,
            'message' => 'Incident déclaré avec succès',
            'incident_id' => $incidentId,
            'client' => $client->name,
            'severity' => $request->severity,
            'date_reported' => now()->toDateTimeString()
        ]);
    }
}