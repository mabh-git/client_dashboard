 
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau feedback reçu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #031e36;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fbfaf6;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(132, 189, 197, 0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #84bdc5;
        }
        .header h1 {
            color: #031e36;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px 0;
        }
        .feedback-section {
            background-color: #e0eaf4;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .feedback-meta {
            background-color: rgba(132, 189, 197, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .rating {
            font-size: 24px;
            color: #f1c40f;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            color: #6d7a8c;
            font-size: 14px;
            border-top: 1px solid #e0eaf4;
        }
        .button {
            display: inline-block;
            background-color: #84bdc5;
            color: #031e36;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 15px;
        }
        .needs-response {
            font-weight: bold;
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nouveau feedback client</h1>
        </div>
        
        <div class="content">
            <p>Un nouveau feedback a été soumis sur votre site.</p>
            
            <div class="feedback-meta">
                <p><strong>Date de soumission :</strong> {{ $feedback->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Note :</strong> <span class="rating">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5 - $rating) }}</span></p>
                <p><strong>Émotion :</strong> 
                    @if($emotion == 'happy')
                        😊 Satisfait
                    @elseif($emotion == 'neutral')
                        😐 Neutre
                    @elseif($emotion == 'sad')
                        😔 Insatisfait
                    @elseif($emotion == 'excited')
                        🤩 Très satisfait
                    @else
                        Non spécifié
                    @endif
                </p>
                
                @if(!$isAnonymous)
                    <p><strong>De :</strong> {{ $feedback->name ?? 'Nom non fourni' }}</p>
                    <p><strong>Email :</strong> {{ $feedback->email ?? 'Email non fourni' }}</p>
                @else
                    <p><em>Ce feedback a été soumis anonymement</em></p>
                @endif
                
                @if($needsResponse)
                    <p class="needs-response">⚠️ Le client demande une réponse</p>
                @endif
            </div>
            
            <div class="feedback-section">
                <strong>Commentaire :</strong>
                <p>{{ $feedback->text }}</p>
            </div>
            
            <center>
                <a href="{{ url('/admin/feedbacks/' . $feedback->id) }}" class="button">Voir le feedback</a>
            </center>
        </div>
        
        <div class="footer">
            <p>Cette notification a été envoyée automatiquement suite à un nouveau feedback client.</p>
            <p>&copy; {{ date('Y') }} Votre Entreprise. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>