<?php

namespace App\Http\Controllers;

use App\Models\SPST;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SPSTController extends Controller
{
    public function index()
    {
        $spsts = SPST::all();
        return response()->json($spsts);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'url' => 'nullable|string|max:255',
            'message' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $spst = SPST::create($request->all());
        return response()->json($spst, 201);
    }

    public function show($id)
    {
        $spst = SPST::findOrFail($id);
        return response()->json($spst);
    }

    public function update(Request $request, $id)
    {
        $spst = SPST::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'url' => 'nullable|string|max:255',
            'message' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $spst->update($request->all());
        return response()->json($spst);
    }

    public function destroy($id)
    {
        $spst = SPST::findOrFail($id);
        $spst->delete();
        return response()->json(null, 204);
    }
    
    public function getNotifications()
    {
        // Mock data for now, in a real app this would come from a Notification model
        $notifications = [
            [
                'id' => 1,
                'message' => 'Votre visite médicale est programmée pour le 15/06/2025',
                'date' => '2025-05-01',
                'icon' => 'fas fa-calendar-check',
                'read' => false
            ],
            [
                'id' => 2,
                'message' => 'Nouveau document disponible : Attestation de suivi',
                'date' => '2025-04-28',
                'icon' => 'fas fa-file-medical',
                'read' => false
            ],
            [
                'id' => 3,
                'message' => 'Rappel : Questionnaire de santé à compléter avant le 10/05/2025',
                'date' => '2025-04-25',
                'icon' => 'fas fa-clipboard-list',
                'read' => false
            ]
        ];
        
        return response()->json($notifications);
    }
    
    public function markNotificationAsRead($notificationId)
    {
        // Logic to mark notification as read would go here
        return response()->json([
            'message' => 'Notification marquée comme lue'
        ]);
    }
    
    public function getServices()
    {
        // Mock data for services
        $services = [
            [
                'id' => 1,
                'name' => 'Visite médicale périodique',
                'description' => 'Suivi médical obligatoire pour tous les salariés',
                'icon' => 'fas fa-user-md'
            ],
            [
                'id' => 2,
                'name' => 'Ergonomie du poste de travail',
                'description' => 'Évaluation et conseils pour améliorer votre espace de travail',
                'icon' => 'fas fa-chair'
            ],
            [
                'id' => 3,
                'name' => 'Prévention des risques professionnels',
                'description' => 'Identification et gestion des risques liés à votre activité',
                'icon' => 'fas fa-hard-hat'
            ]
        ];
        
        return response()->json($services);
    }
    
    public function requestService(Request $request, $serviceId)
    {
        $validator = Validator::make($request->all(), [
            'details' => 'nullable|string',
            'preferred_date' => 'nullable|date'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Logic to process service request would go here
        
        return response()->json([
            'message' => 'Demande de service enregistrée avec succès',
            'service_id' => $serviceId
        ]);
    }
    
    public function getVisits(Request $request)
    {
        // Mock data for visits
        $visits = [
            [
                'id' => 1,
                'type' => 'Visite médicale périodique',
                'date' => '2025-06-15 10:30:00',
                'doctor' => 'Dr. DUBOIS',
                'location' => 'ASTBTP - MARSEILLE-MICHELET',
                'status' => 'upcoming',
                'icon' => 'fas fa-stethoscope'
            ],
            [
                'id' => 2,
                'type' => 'Examen complémentaire',
                'date' => '2025-07-05 14:00:00',
                'doctor' => 'Dr. BLANC',
                'location' => 'ASTBTP - MARSEILLE-MICHELET',
                'status' => 'upcoming',
                'icon' => 'fas fa-heartbeat'
            ],
            [
                'id' => 3,
                'type' => 'Visite de reprise',
                'date' => '2025-01-10 09:15:00',
                'doctor' => 'Dr. MARTIN',
                'location' => 'ASTBTP - MARSEILLE-MICHELET',
                'status' => 'completed',
                'icon' => 'fas fa-user-md'
            ]
        ];
        
        return response()->json($visits);
    }
}