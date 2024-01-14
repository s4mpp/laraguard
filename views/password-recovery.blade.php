@extends('laraguard::layout')

@section('title', 'Recuperar senha')

@section('content')

	<form method="post" action="{{ route($guard->getRouteName('send_link_password')) }}">
		@csrf

		<div>
			<label>E-mail</label>
			<input required type="email" name="email" value="{{ old('email') }}">

			<button type="submit">Enviar e-mail</button>
		</div>

		<a tabindex="-1" href="{{route($guard->getRouteName('login')) }}">Voltar</a>
	</form>
@endsection