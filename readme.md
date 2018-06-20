###client_credentials

 ´´´php artisan passport:client´´´
 
 URL
 {{apiUrl}}/oauth/token

 PARAMS 
grant_type:client_credentials
client_id:3
client_secret:caYh3a586vrVY0i4k8w1soKVf0JX7YJAj13SwzHY

###client_credentials Password
 ´´´php artisan passport:client --password ´´´
 
  URL
  {{apiUrl}}/oauth/token
  
 PARAMS
 grant_type:password
 client_id:4
 client_secret:oKbsNK9hfLuRUq4Pn0iCUm5d85KdLHEWzHuRrTjs
 username:wilson48@example.com
 password:secret
 
 Obtener Token con codigo de autorizacion
 URL
 {{apiUrl}}/oauth/token
 grant_type:authorization_code
 client_id:6
 client_secret:NdUvYmXom9gweaja7kNXRySy36gX3NZBFLCtdBJ5
 redirect_uri:http://localhost.test/
 code:[code que se obtiene de la url /oauth/authorize?client_id=6&redirect_url=localhos&response_type=code]
 
 Obtener Token directamente, Habilitar en authserviceproviders
 /oauth/authorize?client_id=6&redirect_url=localhos&response_type=token
 
 