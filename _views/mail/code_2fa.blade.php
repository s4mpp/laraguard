<p>Olá, {{ Str::words($user->name, 1, '') }}!</p>

<p class="mb-0">Insira o seguinte código para acessar sua conta:</p>

<p class="mb-0">Código: {{ $code }}</p>