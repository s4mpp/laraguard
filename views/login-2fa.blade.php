@extends('laraguard::layout')

@section('title', 'Login 2FA')

@section('content')

	<form method="post" action="{{ route($guard->getRouteName('attempt_login_2fa'), compact('code')) }}">
		@csrf

		<div>
			<label>Code</label>
			<input required type="text" name="code" minlength="6" maxlength="6">
			
			<button type="submit">Submit</button>
		</div>

	</form>
@endsection
