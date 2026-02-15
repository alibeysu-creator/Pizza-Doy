@component('mail::message')
    # Bestellbenachrichtigung

    Hallo {{ $name }},

    Bestellnummer: {{$orderId}}
    {{$message}}

    Vielen Dank,
    {{ config('app.name') }}
@endcomponent