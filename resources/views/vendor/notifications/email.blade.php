<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4; line-height: 1.6; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 600px;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #2B7A78; padding: 30px 40px; text-align: center; border-radius: 8px 8px 0 0;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: bold;">CEC Condat</h1>
                            <p style="color: #ffffff; margin: 8px 0 0 0; font-size: 16px; opacity: 0.9;">Club d'Éducation Canine de Condat-Sur-Vienne</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            {{-- Greeting --}}
                            @if (! empty($greeting))
                                <h2 style="color: #2B7A78; margin: 0 0 24px 0; font-size: 24px; font-weight: 600;">{{ $greeting }}</h2>
                            @else
                                @if (isset($level) && $level === 'error')
                                    <h2 style="color: #2B7A78; margin: 0 0 24px 0; font-size: 24px; font-weight: 600;">Oups !</h2>
                                @else
                                    <h2 style="color: #2B7A78; margin: 0 0 24px 0; font-size: 24px; font-weight: 600;">Bonjour !</h2>
                                @endif
                            @endif

                            {{-- Intro Lines --}}
                            @if (isset($introLines) && is_array($introLines))
                                @foreach ($introLines as $line)
                                    @if (!empty(trim($line)))
                                        <p style="color: #555; margin: 0 0 16px 0; font-size: 16px; line-height: 1.6;">{!! $line !!}</p>
                                    @endif
                                @endforeach
                            @endif

                            {{-- Action Button --}}
                            @isset($actionText)
                                <table width="100%" cellpadding="0" cellspacing="0" style="margin: 32px 0;">
                                    <tr>
                                        <td align="center">
                                            <table cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="background-color: #3AAFA9; border-radius: 6px; padding: 16px 32px;">
                                                        <a href="{{ $actionUrl }}" style="color: #ffffff; text-decoration: none; font-weight: bold; font-size: 16px; display: inline-block;">{{ $actionText }}</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            @endisset

                            {{-- Outro Lines --}}
                            @if (isset($outroLines) && is_array($outroLines))
                                @foreach ($outroLines as $line)
                                    @if (!empty(trim($line)))
                                        <p style="color: #555; margin: 0 0 16px 0; font-size: 16px; line-height: 1.6;">{!! $line !!}</p>
                                    @endif
                                @endforeach
                            @endif

                            {{-- Salutation --}}
                            @if (! empty($salutation))
                                <p style="color: #555; margin: 32px 0 0 0; font-size: 16px; line-height: 1.6;">{!! $salutation !!}</p>
                            @else
                                <p style="color: #555; margin: 32px 0 0 0; font-size: 16px; line-height: 1.6;">Cordialement,<br><strong style="color: #2B7A78;">L'équipe CEC Condat</strong></p>
                            @endif

                            {{ $slot ?? '' }}
                        </td>
                    </tr>

                    {{-- Subcopy --}}
                    @isset($actionText)
                        <tr>
                            <td style="background-color: #f8f9fa; padding: 24px 40px; border-top: 1px solid #e9ecef;">
                                <p style="color: #666; margin: 0 0 12px 0; font-size: 14px;">
                                    <strong style="color: #2B7A78;">Lien alternatif :</strong><br>
                                    Si vous avez des difficultés à cliquer sur le bouton "{{ $actionText }}", copiez et collez l'URL ci-dessous
                                    dans votre navigateur web :
                                </p>
                                <p style="color: #666; margin: 0; font-size: 13px; word-break: break-all; background-color: #ffffff; padding: 8px 12px; border-radius: 4px; border: 1px solid #e9ecef; font-family: monospace;">{{ $displayableActionUrl }}</p>
                            </td>
                        </tr>
                    @endisset

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px 40px; text-align: center; border-radius: 0 0 8px 8px; border-top: 1px solid #e9ecef;">
                            <p style="color: #666; margin: 0 0 4px 0; font-size: 14px;"><strong style="color: #2B7A78;">CEC Condat</strong></p>
                            <p style="color: #666; margin: 0 0 4px 0; font-size: 14px;">Club d'Éducation Canine de Condat-Sur-Vienne</p>
                            <p style="color: #666; margin: 0 0 16px 0; font-size: 14px;">cec-condat@yahoo.fr | ceccondat.e-monsite.com/</p>
                            <p style="color: #999; margin: 0; font-size: 12px;">Cet email a été envoyé automatiquement. Merci de ne pas y répondre directement.</p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
