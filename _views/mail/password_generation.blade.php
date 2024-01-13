<p>Olá, {{ Str::words($user->name, 1, '') }}!</p>

<p>Foi gerada uma nova senha para você!</p>
<p class="mb-0">Clique no botão abaixo ou copie e cole a url no seu navegador para acessar:</p>

<p class="mb-0">E-mail: <strong>{{ $user->email }}</strong></p>
<p class="mb-0">Senha: {{ $password }}</p>

<a class="btn" href="{{ $link }}">Acessar</a>

<p><a href="{{ $link }}">{{ $link }}</a></p>
