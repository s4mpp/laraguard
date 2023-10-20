@extends('laraguard::layout')

@section('title', 'Recuperar senha')

@section('content')

	<form method="post" action="{{ route(RoutesGuard::recoveryPassword()) }}">
		@csrf

		<div>
			<label>E-mail</label>
			<input required type="email" name="email">

			<button type="submit">Enviar e-mail</button>
		</div>


		<a tabindex="-1" href="{{route(RoutesGuard::login()) }}">Voltar</a>
	</form>
@endsection