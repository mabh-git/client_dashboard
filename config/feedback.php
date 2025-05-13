<?php

// config/feedback.php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration du système de feedback
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient les paramètres de configuration du système de feedback.
    |
    */

    // Email de l'administrateur qui recevra les notifications
    'admin_email' => env('FEEDBACK_ADMIN_EMAIL', 'admin@example.com'),

    // Activer/désactiver les notifications pour les nouveaux feedbacks
    'notify_on_new' => env('FEEDBACK_NOTIFY_ON_NEW', true),

    // Liste des catégories disponibles
    'categories' => [
        'Service client',
        'Qualité',
        'Rapidité',
        'Interface utilisateur',
        'Facilité d\'utilisation',
        'Accessibilité',
        'Suggestions'
    ],

    // Délai de réponse cible (en heures) pour les feedbacks demandant une réponse
    'response_target_time' => 48,

    // Limite de feedbacks par adresse IP par jour (pour éviter le spam)
    'rate_limit' => [
        'enabled' => true,
        'max_per_day' => 5,
    ],
];