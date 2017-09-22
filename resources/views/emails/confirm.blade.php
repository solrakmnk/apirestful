@component('mail::message')

    Hola {{$user->name}}

    Cambiaste tu email, confirma tu nueva direccion

    @component('mail::button', ['url' => route('verify',$user->verification_token)])
    Confirmar cuenta
    @endcomponent

    Gracias,<br>
    {{ config('app.name') }}
@endcomponent
