@extends('laraguard::layout')

@section('title', 'Login')

@section('content')

	<form method="post" action="{{ route($guard->getRouteName('attempt_login')) }}">
		@csrf

		<div>
			<label>{{ $guard->getFieldUsername('title') }}</label>
			<input  type="text" name="{{ $guard->getFieldUsername('field') }}" value="{{ old($guard->getFieldUsername('field')) }}">

			<label>Password</label>
			<input   type="password" name="password">
			
			<button type="submit">Login</button>
		</div>


		<a tabindex="-1" href="{{ route($guard->getRouteName('recovery_password')) }}">Esqueceu sua senha?</a>
	</form>
@endsection
