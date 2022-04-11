<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
</head>
<body>
    <h2>Hola {{ $username_usuario }}, se ha autorizado tu petición!</h2>
    <p>Tu código de autorización es: </p>
    <p>{{ $email_code_usuario }}</p>
</body>
</html>