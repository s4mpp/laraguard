<p>Olá, {{ Str::words($user->name, 1, '') }}!</p>

<p>Você solicitou uma recuperação de senha no site <strong>{{ env('APP_NAME') }}</strong>.</p>
<p class="mb-0">Clique no botão abaixo ou copie e cole a url no seu navegador para redefinir sua senha:</p>

<a class="btn" href="{{ $link }}">Alterar minha senha</a>

<p><a href="{{ $link }}">{{ $link }}</a></p>