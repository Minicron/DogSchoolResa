<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email personnalisé</title>
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
        .button {
            display: inline-block;
            background-color: #3AAFA9;
            color: #17252A;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CEC Condat</h1>
        <p>Club d'Éducation Canine de Condat-Sur-Vienne</p>
    </div>
    
    <div class="content">
        <h2>Bonjour {{ $user->firstname ?? 'Utilisateur' }} {{ $user->name ?? '' }},</h2>
        
        <div>
            @if(is_string($message) && !empty($message))
                {!! nl2br(e($message)) !!}
            @else
                <p>Vous avez reçu cet email du Club d'Éducation Canine de Condat-Sur-Vienne.</p>
                <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
            @endif
        </div>
        
        @if(isset($data['action_url']) && isset($data['action_text']))
            <a href="{{ $data['action_url'] }}" class="button">{{ $data['action_text'] }}</a>
        @endif
        
        <p>Cordialement,<br>L'équipe CEC Condat</p>
    </div>
    
    <div class="footer">
        <p>Cet email a été envoyé par le système de notification du Club d'Éducation Canine de Condat-Sur-Vienne.</p>
        <p>Si vous ne souhaitez plus recevoir ces emails, contactez-nous.</p>
    </div>
</body>
</html>
