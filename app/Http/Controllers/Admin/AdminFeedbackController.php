<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\FeedbackResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Mail\FeedbackResponseMail;
use Illuminate\Support\Facades\Mail;

class AdminFeedbackController extends Controller
{
    /**
     * Affiche le tableau de bord des feedbacks
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Statistiques pour le tableau de bord
        $stats = [
            'total_count' => Feedback::count(),
            'average_rating' => round(Feedback::avg('rating') ?? 0, 1),
            'pending_response' => Feedback::where('want_response', true)
                                         ->where('is_resolved', false)
                                         ->count(),
            'recent_count' => Feedback::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // Distribution des notes par mois (pour graphique)
        $monthlyRatings = DB::table('feedbacks')
            ->select(DB::raw('YEAR(created_at) as year'), 
                    DB::raw('MONTH(created_at) as month'), 
                    DB::raw('AVG(rating) as average_rating'),
                    DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Distribution par catégorie
        $categoryDistribution = [];
        $allFeedbacks = Feedback::all();
        
        foreach ($allFeedbacks as $feedback) {
            if (!empty($feedback->categories)) {
                foreach ($feedback->categories as $category) {
                    if (!isset($categoryDistribution[$category])) {
                        $categoryDistribution[$category] = 0;
                    }
                    $categoryDistribution[$category]++;
                }
            }
        }
        
        // Trier par nombre décroissant
        arsort($categoryDistribution);

        return view('admin.feedback.dashboard', [
            'stats' => $stats,
            'monthlyRatings' => $monthlyRatings,
            'categoryDistribution' => $categoryDistribution
        ]);
    }

    /**
     * Affiche la liste des feedbacks
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Feedback::query()->orderBy('created_at', 'desc');

        // Filtrage
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->has('requires_response')) {
            $query->where('want_response', true)
                  ->where('is_resolved', false);
        }

        if ($request->has('emotion')) {
            $query->where('emotion', $request->emotion);
        }

        // Recherche textuelle
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('text', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $feedbacks = $query->paginate(15);

        return view('admin.feedback.index', [
            'feedbacks' => $feedbacks
        ]);
    }

    /**
     * Affiche un feedback spécifique
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(int $id)
    {
        $feedback = Feedback::findOrFail($id);
        $responses = $feedback->responses()->with('user')->get();

        return view('admin.feedback.show', [
            'feedback' => $feedback,
            'responses' => $responses
        ]);
    }

    /**
     * Formulaire de réponse à un feedback
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showResponseForm(int $id)
    {
        $feedback = Feedback::findOrFail($id);
        
        // Vérifier que le feedback demande une réponse et a un email
        if (!$feedback->want_response || !$feedback->email) {
            return redirect()->route('admin.feedbacks.show', $id)
                             ->with('error', 'Ce feedback ne nécessite pas de réponse ou ne contient pas d\'adresse email');
        }

        return view('admin.feedback.respond', [
            'feedback' => $feedback
        ]);
    }

    /**
     * Traite la réponse à un feedback
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function respond(Request $request, int $id)
    {
        $request->validate([
            'message' => 'required|string|min:10',
        ]);

        $feedback = Feedback::findOrFail($id);

        // Vérifier que le feedback demande une réponse et a un email
        if (!$feedback->want_response || !$feedback->email) {
            return redirect()->route('admin.feedbacks.show', $id)
                            ->with('error', 'Ce feedback ne nécessite pas de réponse ou ne contient pas d\'adresse email');
        }

        try {
            // Créer la réponse en base de données
            $response = FeedbackResponse::create([
                'feedback_id' => $id,
                'user_id' => Auth::id(),
                'message' => $request->message,
                'sent_at' => now(),
            ]);

            // Envoyer l'email au client
            Mail::to($feedback->email)->send(new FeedbackResponseMail($feedback, $response));

            // Marquer le feedback comme résolu
            $feedback->is_resolved = true;
            $feedback->save();

            return redirect()->route('admin.feedbacks.show', $id)
                            ->with('success', 'Votre réponse a été envoyée avec succès');
        } catch (\Exception $e) {
            return redirect()->route('admin.feedbacks.show', $id)
                            ->with('error', 'Une erreur est survenue lors de l\'envoi de la réponse: ' . $e->getMessage());
        }
    }

    /**
     * Marque un feedback comme résolu sans réponse
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsResolved(int $id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->is_resolved = true;
        $feedback->save();

        return redirect()->route('admin.feedbacks.index')
                        ->with('success', 'Feedback marqué comme résolu');
    }

    /**
     * Exporte les feedbacks au format CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(Request $request)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="feedbacks_export_' . date('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $query = Feedback::query()->orderBy('created_at', 'desc');
        
        // Application des mêmes filtres que l'index si nécessaire
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }
        
        if ($request->has('requires_response')) {
            $query->where('want_response', true)
                  ->where('is_resolved', false);
        }
        
        $feedbacks = $query->get();
        
        $callback = function() use ($feedbacks) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID', 'Note', 'Émotion', 'Catégories', 'Commentaire', 'Anonyme', 
                'Nom', 'Email', 'Demande réponse', 'Résolu', 'Date de soumission'
            ]);
            
            // Lignes de données
            foreach ($feedbacks as $feedback) {
                fputcsv($file, [
                    $feedback->id,
                    $feedback->rating,
                    $feedback->emotion,
                    is_array($feedback->categories) ? implode(', ', $feedback->categories) : '',
                    $feedback->text,
                    $feedback->is_anonymous ? 'Oui' : 'Non',
                    $feedback->name,
                    $feedback->email,
                    $feedback->want_response ? 'Oui' : 'Non',
                    $feedback->is_resolved ? 'Oui' : 'Non',
                    $feedback->created_at->format('d/m/Y H:i')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}