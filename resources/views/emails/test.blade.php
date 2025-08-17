<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #17252A;
            color: #FEFFFF;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CEC Condat</h1>
        <p>Club d'Éducation Canine de Condat-Sur-Vienne</p>
    </div>
    
    <div class="content">
        <h2>Test d'Email</h2>
        
        <p>Ceci est un test d'envoi d'email depuis l'application DogSchoolResa.</p>
        
        <p><strong>Timestamp:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
        
        @if(isset($testMessage))
            <p><strong>Message de test:</strong> {{ $testMessage }}</p>
        @endif
        
        <p>Cordialement,<br>L'équipe CEC Condat</p>
    </div>
    
    <div class="footer">
        <p>Cet email a été envoyé par le système de test du Club d'Éducation Canine de Condat-Sur-Vienne.</p>
    </div>
</body>
</html>
