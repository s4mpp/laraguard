@extends($panel->layout()->getAuthFile())

@section('title', __('laraguard::password.reset'))

@section('content')

	<div class="bg-gray-100 mb-4 p-4 rounded-lg text-center space-y-3">
		<p>E-mail: <strong>{{ $user->email }}</strong></p>
	</div>


	<form  method="POST" action="{{ route($panel->getRouteName('store_password'), ['token' => $token]) }}">
		@csrf
		@method('PUT')
		

		<input type="hidden" name="email" required value="{{ $user->email }}">
		<input type="hidden" name="token" required value="{{ $token }}">

		<div class="space-y-6">

			<x-element::form.input required type="password" name="password" title="{{ __('laraguard::password.type_new_password') }}" />

			<x-element::form.input required type="password" name="password_confirmation" title="{{ __('laraguard::password.repeat_new_password') }}" />

			<x-element::button full type="submit">{{  __('laraguard::password.change')  }}</x-element::button>
		</div>

		<p class="mt-10 text-center text-sm text-gray-500">
			<a tabindex="-1" href="{{route($panel->getRouteName('login')) }}" class="font-semibold leading-6 text-gray-600 hover:text-gray-500">{{ __('laraguard::login.go_back') }}</a>
		</p>

	</form>
@endsection