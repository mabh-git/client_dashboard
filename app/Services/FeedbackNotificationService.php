<?php

namespace App\Services;

use App\Models\Feedback;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewFeedbackNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FeedbackNotificationService
{
    /**
     * Notifie les administrateurs d'un nouveau feedback
     *
     * @param Feedback $feedback
     * @return void
     */
    public function notifyAdmins(Feedback $feedback): void
    {
        // Vérifier si les notifications sont activées
        if (!config('feedback.notify_on_new', true)) {
            return;
        }

        try {
            // Notification par email
            $this->sendEmailNotification($feedback);
            
            // Notification Slack si configurée
            $this->sendSlackNotification($feedback);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la notification des admins pour un feedback', [
                'error' => $e->getMessage(),
                'feedback_id' => $feedback->id
            ]);
        }
    }

    /**
     * Envoie une notification par email
     *
     * @param Feedback $feedback
     * @return void
     */
    private function sendEmailNotification(Feedback $feedback): void
    {
        $adminEmail = config('feedback.admin_email');
        
        if (!$adminEmail) {
            return;
        }
        
        try {
            Mail::to($adminEmail)->send(new NewFeedbackNotification($feedback));
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi d\'email pour un nouveau feedback', [
                'error' => $e->getMessage(),
                'feedback_id' => $feedback->id
            ]);
        }
    }

    /**
     * Envoie une notification Slack si configurée
     *
     * @param Feedback $feedback
     * @return void
     */
    private function sendSlackNotification(Feedback $feedback): void
    {
        $webhookUrl = config('services.slack.webhook_feedback');
        
        if (!$webhookUrl) {
            return;
        }
        
        // Préparation du message Slack
        $message = [
            'text' => '📝 Nouveau feedback client',
            'attachments' => [
                [
                    'color' => $this->getRatingColor($feedback->rating),
                    'fields' => [
                        [
                            'title' => 'Note',
                            'value' => $this->getStarRating($feedback->rating),
                            'short' => true
                        ],
                        [
                            'title' => 'Émotion',
                            'value' => $this->getEmotionText($feedback->emotion),
                            'short' => true
                        ],
                        [
                            'title' => 'Commentaire',
                            'value' => $feedback->text,
                            'short' => false
                        ]
                    ],
                    'footer' => 'Feedback #' . $feedback->id . ' | ' . $feedback->created_at->format('d/m/Y H:i')
                ]
            ]
        ];
        
        // Si le client demande une réponse, ajouter une action
        if ($feedback->want_response && !$feedback->is_anonymous) {
            $message['attachments'][0]['actions'] = [
                [
                    'type' => 'button',
                    'text' => 'Répondre',
                    'url' => url('/admin/feedbacks/' . $feedback->id)
                ]
            ];
        }
        
        try {
            Http::post($webhookUrl, $message);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de notification Slack pour un nouveau feedback', [
                'error' => $e->getMessage(),
                'feedback_id' => $feedback->id
            ]);
        }
    }

    /**
     * Obtient la couleur correspondant à la note
     *
     * @param int $rating
     * @return string
     */
    private function getRatingColor(int $rating): string
    {
        return match ($rating) {
            1 => '#e74c3c', // Rouge
            2 => '#e67e22', // Orange
            3 => '#f1c40f', // Jaune
            4 => '#2ecc71', // Vert clair
            5 => '#27ae60', // Vert foncé
            default => '#95a5a6', // Gris
        };
    }

    /**
     * Obtient la représentation en étoiles de la note
     *
     * @param int $rating
     * @return string
     */
    private function getStarRating(int $rating): string
    {
        return str_repeat('⭐', $rating) . str_repeat('☆', 5 - $rating);
    }

    /**
     * Obtient la représentation textuelle de l'émotion
     *
     * @param string|null $emotion
     * @return string
     */
    private function getEmotionText(?string $emotion): string
    {
        return match ($emotion) {
            'happy' => '😊 Satisfait',
            'neutral' => '😐 Neutre',
            'sad' => '😔 Insatisfait',
            'excited' => '🤩 Très satisfait',
            default => 'Non spécifié',
        };
    }
}