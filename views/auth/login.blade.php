@extends('laraguard::auth.main')

@section('content')

	<form class="space-y-6" method="POST" action="{{ route($panel->getRouteName('attempt_login')) }}">
		@csrf

		<div>
			<label for="email" class="block text-sm font-medium leading-6 text-gray-900">{{ $panel->getFieldUsername('title') }}</label>
			<div class="mt-2">
			  <input name="{{ $panel->getFieldUsername('field') }}" type="text" required value="{{ old($panel->getFieldUsername('field')) }}" class="
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
			<label for="email" class="block text-sm font-medium leading-6 text-gray-900">{{ __('laraguard::login.password') }}</label>
			<div class="mt-2">
			  <input name="password" type="password" required class="
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

		<div class="flex items-center justify-center">
			{{-- <div class="flex items-center">
				<input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-600">
				<label for="remember-me" class="ml-3 block text-sm leading-6 text-gray-700">Remember me</label>
			</div> --}}

			<div class="text-sm leading-6">
				<a tabindex="-1"  href="{{ route($panel->getRouteName('recovery_password')) }}" class="font-semibold text-gray-600 hover:text-gray-500">{{ __('laraguard::login.lost_your_password') }}</a>
			</div>
		</div>

		<div>
			<button type="submit" class="ease-linear transition  flex w-full justify-center rounded-md bg-gray-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">{{ __('laraguard::login.to_enter') }}</button>
		</div>
		
	</form>
@endsection