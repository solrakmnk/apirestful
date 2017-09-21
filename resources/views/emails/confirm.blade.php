Hola {{$user->name}}
Cambiaste tu email, confirma tu nueva direccion
{{route('verify',$user->verification_token)}}