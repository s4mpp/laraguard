@extends('laraguard::layout')

@section('title', 'Login')

@section('content')

	<form method="post" action="{{ route(S4mpp\Laraguard\Routes::attemptLogin()) }}">
		@csrf

		<div>
			<label>E-mail</label>
			<input required type="text" name="email">

			<label>Password</label>
			<input required type="text" name="password">
			
			<button type="submit">Login</button>
		</div>


		<a tabindex="-1" href="{{route(S4mpp\Laraguard\Routes::forgotPassword()) }}">Esqueceu sua senha?</a>
	</form>
@endsection
