@extends('laraguard::layout')

@section('title', 'Recuperar senha')

@section('content')

	<form method="post" action="{{ route(S4mpp\Laraguard\Routes::recoveryPassword()) }}">
		@csrf

		<div>
			<label>E-mail</label>
			<input required type="email" name="email">

			<button type="submit">Enviar e-mail</button>
		</div>


		<a tabindex="-1" href="{{route(S4mpp\Laraguard\Routes::login()) }}">Voltar</a>
	</form>
@endsection