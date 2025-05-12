<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::query();
        
        // Filtrage par type de document
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        // Filtrage par entité (client, employé, etc.)
        if ($request->has('documentable_type') && $request->has('documentable_id')) {
            $query->where('documentable_type', $request->documentable_type)
                  ->where('documentable_id', $request->documentable_id);
        }
        
        $documents = $query->paginate($request->input('per_page', 10));
        
        return response()->json($documents);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'required|string|max:50',
            'documentable_type' => 'required|string',
            'documentable_id' => 'required|integer'
        ]);
        
        // Stockage du fichier
        $path = $request->file('file')->store('documents');
        
        $document = Document::create([
            'name' => $request->name,
            'file_path' => $path,
            'type' => $request->type,
            'documentable_type' => $request->documentable_type,
            'documentable_id' => $request->documentable_id
        ]);
        
        return response()->json($document, 201);
    }

    public function show($id)
    {
        $document = Document::findOrFail($id);
        return response()->json($document);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'string|max:255',
            'file' => 'nullable|file|max:10240', // 10MB max
            'type' => 'string|max:50'
        ]);
        
        $document = Document::findOrFail($id);
        
        $data = [
            'name' => $request->name,
            'type' => $request->type
        ];
        
        // Mise à jour du fichier si fourni
        if ($request->hasFile('file')) {
            // Supprimer l'ancien fichier
            Storage::delete($document->file_path);
            
            // Stocker le nouveau fichier
            $path = $request->file('file')->store('documents');
            $data['file_path'] = $path;
        }
        
        $document->update($data);
        
        return response()->json($document);
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        
        // Supprimer le fichier physique
        Storage::delete($document->file_path);
        
        // Supprimer l'enregistrement
        $document->delete();
        
        return response()->json([
            'message' => 'Document supprimé avec succès'
        ]);
    }
    
    public function download($id)
    {
        $document = Document::findOrFail($id);
        
        if (!Storage::exists($document->file_path)) {
            return response()->json(['error' => 'Fichier non trouvé'], 404);
        }
        
        return Storage::download($document->file_path, $document->name);
    }
}