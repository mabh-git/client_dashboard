<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\FeedbackResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use App\Mail\FeedbackResponseMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class FeedbackResponseController extends Controller
{
    /**
     * Envoi une réponse à un feedback client
     * 
     * @param Request $request
     * @param int $feedbackId
     * @return JsonResponse
     */
    public function respond(Request $request, int $feedbackId): JsonResponse
    {
        // Valider les données de la requête
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        // Trouver le feedback concerné
        $feedback = Feedback::findOrFail($feedbackId);

        // Vérifier que le feedback demande une réponse et a un email
        if (!$feedback->want_response || !$feedback->email) {
            return response()->json([
                'message' => 'Ce feedback ne nécessite pas de réponse ou ne contient pas d\'adresse email'
            ], 400);
        }

        try {
            // Créer la réponse en base de données
            $response = FeedbackResponse::create([
                'feedback_id' => $feedbackId,
                'user_id' => Auth::id(),
                'message' => $request->message,
                'sent_at' => now(),
            ]);

            // Envoyer l'email au client
            if ($feedback->email) {
                try {
                    Mail::to($feedback->email)->send(new FeedbackResponseMail($feedback, $response));
                } catch (\Exception $e) {
                    // Logger l'erreur mais continuer l'exécution
                    \Log::error('Erreur lors de l\'envoi du mail de réponse au feedback: ' . $e->getMessage());
                }
            }

            // Marquer le feedback comme résolu
            $feedback->is_resolved = true;
            $feedback->save();

            return response()->json([
                'message' => 'Réponse envoyée avec succès',
                'response' => $response
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'envoi de la réponse',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère l'historique des réponses pour un feedback
     * 
     * @param int $feedbackId
     * @return JsonResponse
     */
    public function getResponses(int $feedbackId): JsonResponse
    {
        $feedback = Feedback::findOrFail($feedbackId);
        $responses = FeedbackResponse::where('feedback_id', $feedbackId)
                                  ->with('user:id,name')
                                  ->orderBy('sent_at', 'desc')
                                  ->get();

        return response()->json($responses);
    }
}