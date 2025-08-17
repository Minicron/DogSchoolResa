<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #17252A 0%, #2B7A78 100%);
            color: #ffffff;
            padding: 30px 40px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #ffffff;
        }
        .header p {
            margin: 8px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
            color: #ffffff;
        }
        .content {
            padding: 40px;
            color: #333;
        }
        .content h1 {
            color: #17252A;
            font-size: 24px;
            margin-bottom: 25px;
            font-weight: 600;
            padding: 0;
        }
        .content p {
            margin-bottom: 18px;
            color: #555;
            font-size: 16px;
            line-height: 1.6;
        }
        .content strong {
            color: #2B7A78;
            font-weight: 600;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #3AAFA9 0%, #2B7A78 100%);
            color: #ffffff;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 25px 0;
            text-align: center;
            box-shadow: 0 4px 15px rgba(59, 175, 169, 0.3);
            transition: all 0.3s ease;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 175, 169, 0.4);
        }
        .footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 30px 40px;
            border-top: 1px solid #e9ecef;
            color: #666;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
        .subcopy {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e9ecef;
            font-size: 13px;
            color: #666;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        .break-all {
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>CEC Condat</h1>
            <p>Club d'Éducation Canine de Condat-Sur-Vienne</p>
        </div>
        
        <div class="content">
            {{-- Greeting --}}
            @if (! empty($greeting))
                <h1>{{ $greeting }}</h1>
            @else
                @if (isset($level) && $level === 'error')
                    <h1>Oups !</h1>
                @else
                    <h1>Bonjour !</h1>
                @endif
            @endif

            {{-- Intro Lines --}}
            @if (isset($introLines) && is_array($introLines))
                @foreach ($introLines as $line)
                    @if (!empty(trim($line)))
                        <p>{!! $line !!}</p>
                    @else
                        <br>
                    @endif
                @endforeach
            @endif

            {{-- Action Button --}}
            @isset($actionText)
                <div style="text-align: center;">
                    <a href="{{ $actionUrl }}" class="button">
                        {{ $actionText }}
                    </a>
                </div>
            @endisset

            {{-- Outro Lines --}}
            @if (isset($outroLines) && is_array($outroLines))
                @foreach ($outroLines as $line)
                    @if (!empty(trim($line)))
                        <p>{!! $line !!}</p>
                    @else
                        <br>
                    @endif
                @endforeach
            @endif

            {{-- Salutation --}}
            @if (! empty($salutation))
                <p>{!! $salutation !!}</p>
            @else
                <p>Cordialement,<br><strong>L'équipe CEC Condat</strong></p>
            @endif
        </div>

        {{-- Subcopy --}}
        @isset($actionText)
            <div class="subcopy">
                <p>
                    <strong>Lien alternatif :</strong><br>
                    Si vous avez des difficultés à cliquer sur le bouton "{{ $actionText }}", copiez et collez l'URL ci-dessous
                    dans votre navigateur web :<br>
                    <span class="break-all">{{ $displayableActionUrl }}</span>
                </p>
            </div>
        @endisset

        <div class="footer">
            <p><strong>CEC Condat</strong></p>
            <p>cec-condat@yahoo.fr | ceccondat.e-monsite.com/</p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                Cet email a été envoyé automatiquement. Merci de ne pas y répondre directement.
            </p>
        </div>
    </div>
</body>
</html>
