@extends('laraguard::auth.main')

@section('title', __('laraguard::password.recovery'))

@section('content')

	<form class="space-y-6"  method="POST" action="{{ route($panel->getRouteName('send_link_password')) }}">
		@csrf

		<div>
			<label for="email" class="block text-sm font-medium leading-6 text-gray-900">E-mail</label>
			<div class="mt-2">
			  <input name="email" type="text" required value="{{ old('email') }}" class="
				block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm 
				px-3
				ring-none
				placeholder:text-gray-400 
				outline-none
				ring-1 ring-inset ring-gray-300
				focus:ring-gray-600
				focus:ring-2 focus:ring-inset
				transition ease-linear duration-200
				sm:text-sm sm:leading-6">
			</div>
		</div>

		<div>
			<button type="submit" class="ease-linear transition  flex w-full justify-center rounded-md bg-gray-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">{{ __('laraguard::password.send_email') }}</button>
		</div>

		<p class="mt-10 text-center text-sm text-gray-500">
			<a tabindex="-1" href="{{route($panel->getRouteName('login')) }}" class="font-semibold leading-6 text-gray-600 hover:text-gray-500">{{ __('laraguard::login.go_back') }}</a>
		</p>
	</form>
@endsection