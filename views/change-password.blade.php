@extends('laraguard::layout')

@section('title', 'Alterar senha')

@section('content')

	<p>E-mail: <strong>{{ $user->email }}</strong></p>

	<form method="post" action="{{ route($guard->getRouteName('storePassword'), ['token' => $token]) }}">
		@csrf
		@method('PUT')

		<input type="text" name="email" required value="{{ $user->email }}">
		<input type="text" name="token" required value="{{ $token }}">

		<div>
			<label>Senha</label>
			<input required type="password" name="password">

			<label>Digite a nova senha</label>
			<input required type="password" name="password_confirmation">


			<button type="submit">Alterar senha</button>
		</div>


		<a tabindex="-1" href="{{route($guard->getRouteName('login')) }}">Voltar</a>
	</form>
@endsection