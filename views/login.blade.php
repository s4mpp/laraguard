@extends('laraguard::layout')

@section('title', __('laraguard::login.title'))

@section('content')

	<form method="post" action="{{ route($guard->getRouteName('attempt_login')) }}">
		@csrf

		<div>
			<label>{{ $guard->getFieldUsername('title') }}</label>
			<input  type="text" name="{{ $guard->getFieldUsername('field') }}" value="{{ old($guard->getFieldUsername('field')) }}">

			<label>{{ __('laraguard::login.password') }}</label>
			<input   type="password" name="password">
			
			<button type="submit">{{ __('laraguard::login.to_enter') }}</button>
		</div>


		<a tabindex="-1" href="{{ route($guard->getRouteName('recovery_password')) }}">{{ __('laraguard::login.lost_your_password') }}</a>
	</form>
@endsection
