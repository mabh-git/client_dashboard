<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::query();
        
        // Filtrage par utilisateur
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filtrage par statut de lecture
        if ($request->has('read')) {
            $query->where('read', $request->read === 'true');
        }
        
        // Tri par date (plus récentes d'abord)
        $query->orderBy('created_at', 'desc');
        
        $notifications = $query->get();
        
        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'icon' => 'nullable|string|max:50',
            'severity' => 'nullable|string|in:low,medium,high'
        ]);
        
        $notification = Notification::create($request->all());
        
        return response()->json($notification, 201);
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        return response()->json($notification);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'string|max:255',
            'message' => 'string',
            'icon' => 'nullable|string|max:50',
            'severity' => 'nullable|string|in:low,medium,high',
            'read' => 'boolean'
        ]);
        
        $notification = Notification::findOrFail($id);
        $notification->update($request->all());
        
        return response()->json($notification);
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        
        return response()->json([
            'message' => 'Notification supprimée avec succès'
        ]);
    }
    
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->read = true;
        $notification->save();
        
        return response()->json($notification);
    }
    
    public function markAllAsRead(Request $request)
    {
        $query = Notification::where('read', false);
        
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        $count = $query->count();
        $query->update(['read' => true]);
        
        return response()->json([
            'message' => $count . ' notifications marquées comme lues'
        ]);
    }
}