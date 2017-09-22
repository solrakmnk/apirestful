@component('mail::message')

    Hola {{$user->name}}

    Gracias por crear una cuenta

    @component('mail::button', ['url' => route('verify',$user->verification_token)])
        Confirmar cuenta
    @endcomponent

    Gracias,<br>
    {{ config('app.name') }}
@endcomponent
