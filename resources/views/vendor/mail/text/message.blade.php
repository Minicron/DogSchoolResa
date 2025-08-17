{{-- Greeting --}}
@if (! empty($greeting))
{{ $greeting }}
@else
@if (isset($level) && $level === 'error')
Oups !
@else
Bonjour !
@endif
@endif

{{-- Intro Lines --}}
@if (isset($introLines) && is_array($introLines))
@foreach ($introLines as $line)
{{ $line }}

@endforeach
@endif

{{-- Action Button --}}
@isset($actionText)
{{ $actionText }}: {{ $actionUrl }}

@endisset

{{-- Outro Lines --}}
@if (isset($outroLines) && is_array($outroLines))
@foreach ($outroLines as $line)
{{ $line }}

@endforeach
@endif

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
Cordialement,
{{ config('app.name') }}
@endif

{{-- Subcopy --}}
@isset($actionText)
@lang(
    "Si vous avez des difficultés à cliquer sur le bouton \":actionText\", copiez et collez l'URL ci-dessous\n".
    'dans votre navigateur web:',
    [
        'actionText' => $actionText,
    ]
) {{ $displayableActionUrl }}
@endisset
