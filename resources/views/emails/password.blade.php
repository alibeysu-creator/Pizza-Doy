@component('mail::message')
    # Passwort zurücksetzen

    Ihr Code ist {{$pin}}

    Bitte geben Sie Ihren Einmalcode nicht an Dritte weiter.
    Sie haben eine Anfrage zum Zurücksetzen Ihres Passworts gestellt.
    Bitte verwerfen Sie diese, wenn Sie es nicht waren.

    Vielen Dank,
    {{ config('app.name') }}
@endcomponent
