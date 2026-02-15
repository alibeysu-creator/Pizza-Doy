@component('mail::message')
    # Abonnenten-Benachrichtigung

    Hallo,

    Betreff: {{ $title }}
    {{ $message }}

    Danke,
    {{ config('app.name') }}
@endcomponent
