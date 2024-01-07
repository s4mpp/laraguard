@extends('laraguard::layout')

@section('title', 'Login')

@section('content')

	<form method="post" action="{{ route(RoutesGuard::identifier($route_identifier)->attemptLogin()) }}">
		@csrf

		<div>
			<label>E-mail</label>
			<input required type="text" name="username" value="{{ old('username') }}">

			<label>Password</label>
			<input required type="password" name="password">
			
			<button type="submit">Login</button>
		</div>


		<a tabindex="-1" href="{{route(RoutesGuard::identifier($route_identifier)->forgotPassword()) }}">Esqueceu sua senha?</a>
	</form>
@endsection
