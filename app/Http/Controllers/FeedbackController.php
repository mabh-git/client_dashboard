<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Http\Requests\FeedbackRequest;
use App\Services\FeedbackNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    /**
     * @var FeedbackNotificationService
     */
    protected $notificationService;

    /**
     * Constructor
     */
    public function __construct(FeedbackNotificationService $notificationService = null)
    {
        $this->notificationService = $notificationService ?? new FeedbackNotificationService();
    }

    /**
     * Enregistre un nouveau feedback.
     *
     * @param  \App\Http\Requests\FeedbackRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(FeedbackRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            
            // Créer le feedback avec les données validées
            $feedback = Feedback::create([
                'rating' => $validatedData['rating'],
                'emotion' => $validatedData['emotion'] ?? null,
                'text' => $validatedData['text'],
                'categories' => $validatedData['categories'] ?? [],
                'is_anonymous' => $validatedData['is_anonymous'],
                'name' => $validatedData['is_anonymous'] ? null : ($validatedData['name'] ?? null),
                'email' => $validatedData['is_anonymous'] ? null : ($validatedData['email'] ?? null),
                'want_response' => $validatedData['is_anonymous'] ? false : ($validatedData['want_response'] ?? false),
                'is_resolved' => false,
            ]);

            // Notifier les administrateurs
            if ($this->notificationService) {
                $this->notificationService->notifyAdmins($feedback);
            }

            return response()->json([
                'message' => 'Votre avis a bien été enregistré, merci !',
                'feedback_id' => $feedback->id
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'enregistrement du feedback: ' . $e->getMessage());
            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'enregistrement de votre avis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère la liste des feedbacks pour l'administration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Cette méthode serait protégée par authentification dans un vrai système
        $query = Feedback::query();

        // Filtres optionnels
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->has('resolved')) {
            $query->where('is_resolved', $request->boolean('resolved'));
        }

        if ($request->has('requires_response')) {
            $query->where('want_response', true)
                  ->whereNotNull('email')
                  ->where('is_resolved', false);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $feedbacks = $query->orderBy('created_at', 'desc')
                         ->paginate($perPage);

        return response()->json($feedbacks);
    }

    /**
     * Récupère un feedback spécifique.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $feedback = Feedback::with('responses')->findOrFail($id);
        return response()->json($feedback);
    }

    /**
     * Marque un feedback comme résolu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsResolved(int $id): JsonResponse
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->is_resolved = true;
        $feedback->save();

        return response()->json([
            'message' => 'Feedback marqué comme résolu',
            'feedback' => $feedback
        ]);
    }

    /**
     * Obtient des statistiques sur les feedbacks.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total' => Feedback::count(),
            'average_rating' => round(Feedback::avg('rating') ?? 0, 1),
            'rating_distribution' => [
                '1' => Feedback::where('rating', 1)->count(),
                '2' => Feedback::where('rating', 2)->count(),
                '3' => Feedback::where('rating', 3)->count(),
                '4' => Feedback::where('rating', 4)->count(),
                '5' => Feedback::where('rating', 5)->count(),
            ],
            'emotions' => [
                'happy' => Feedback::where('emotion', 'happy')->count(),
                'neutral' => Feedback::where('emotion', 'neutral')->count(),
                'sad' => Feedback::where('emotion', 'sad')->count(),
                'excited' => Feedback::where('emotion', 'excited')->count(),
            ],
            'pending_responses' => Feedback::where('want_response', true)
                                         ->where('is_resolved', false)
                                         ->count(),
        ];

        return response()->json($stats);
    }
}