@extends('laraguard::layout')

@section('title', 'Alterar senha')

@section('content')

	<p>E-mail: <strong>{{ $user->email }}</strong></p>

	<form method="post" action="{{ route(RoutesGuard::identifier($route_identifier)->storePasswordRecovery(), ['token_password_recovery' => $token_password_recovery]) }}">
		@csrf
		@method('put')

		<div>
			<label>Senha</label>
			<input required type="password" name="password">

			<label>Digite a nova senha</label>
			<input required type="password" name="password_confirmation">


			<button type="submit">Alterar senha</button>
		</div>


		<a tabindex="-1" href="{{route(RoutesGuard::identifier($route_identifier)->login()) }}">Voltar</a>
	</form>
@endsection