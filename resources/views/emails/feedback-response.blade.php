 
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réponse à votre commentaire</title>
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
        .response-section {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #84bdc5;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Réponse à votre commentaire</h1>
        </div>
        
        <div class="content">
            <p>Bonjour {{ $clientName }},</p>
            
            <p>Nous vous remercions d'avoir pris le temps de partager votre avis avec nous. Votre feedback est précieux et nous permet d'améliorer continuellement nos services.</p>
            
            <div class="feedback-section">
                <strong>Votre commentaire du {{ $feedback->created_at->format('d/m/Y') }} :</strong>
                <p>{{ $feedback->text }}</p>
            </div>
            
            <p>Voici notre réponse :</p>
            
            <div class="response-section">
                <p>{{ $response->message }}</p>
            </div>
            
            <p>Si vous avez d'autres questions ou commentaires, n'hésitez pas à nous contacter.</p>
            
            <p>Merci encore pour votre feedback !</p>
            
            <center>
                <a href="{{ url('/') }}" class="button">Visiter notre site</a>
            </center>
        </div>
        
        <div class="footer">
            <p>Cet email a été envoyé en réponse à votre demande de suivi concernant un commentaire laissé sur notre site.</p>
            <p>&copy; {{ date('Y') }} Votre Entreprise. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>