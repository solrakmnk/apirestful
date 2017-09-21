Hola {{$user->name}}
Gracias por crear una cuenta, por favor verificala usando el enlace:

{{route('verify',$user->verification_token)}}