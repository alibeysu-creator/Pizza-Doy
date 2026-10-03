@component('mail::message')
    # Bestellbenachrichtigung

    Bestellnummer: {{$orderId}}
    {{$message}}

    Vielen Dank,
    {{ config('app.name') }}
@endcomponent
