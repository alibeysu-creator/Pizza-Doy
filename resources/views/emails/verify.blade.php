@component('mail::message')
    # E-Mail-Verifizierung

    Vielen Dank für Ihre Anmeldung.
    Ihr sechsstelliger Code lautet <h4>{{$pin}}</h4>

    Danke,<br>
    {{ config('app.name') }}
@endcomponent
