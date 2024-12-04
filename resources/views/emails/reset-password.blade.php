<div>
    <h1>Restablecer contraseña</h1>

    <p>Hola {{ $user->name }},</p>
    <p>Recibiste este correo porque se solicitó un restablecimiento de contraseña para tu cuenta.</p>
    <p>Haz clic en el siguiente enlace para restablecer tu contraseña:</p>
    <a
 href="{{ $url }}">Restablecer contraseña</a>
    <p>Este enlace de restablecimiento de contraseña expirará en {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutos.</p>
    <p>Si no solicitaste un restablecimiento de contraseña, puedes ignorar este correo.</p>
</body>
</html>
</div>
