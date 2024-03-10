@extends($panel->layout()->getAuthFile())

@section('title', __('laraguard::login.title'))

@section('content')

	<x-element::message.flash />
	<x-element::message.error />

	<form class="space-y-6" method="POST" action="{{ route($panel->getRouteName('attempt_login')) }}" x-data="{loading:false}" x-on:submit="loading=true">
		@csrf

		<x-element::form.input required type="{{ $panel->getCredential()->getType() }}" name="username" title="{{ $panel->getCredential()->getTitle() }}" />
		
		<x-element::form.input required type="password" name="password" title="{{ __('laraguard::login.password') }}" />

		<x-element::button dusk="login" full type="submit">{{ __('laraguard::login.to_enter') }}</x-element::button>

		<div class="flex items-center justify-center">
			{{-- <div class="flex items-center">
				<input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-600">
				<label for="remember-me" class="ml-3 block text-sm leading-6 text-gray-700">Remember me</label>
			</div> --}}

			<div class="text-sm leading-6">
				<a tabindex="-1"  href="{{ route($panel->getRouteName('recovery_password')) }}" class="font-semibold text-gray-600 hover:text-gray-500">{{ __('laraguard::login.lost_your_password') }}?</a>
			</div>
		</div>

		@if($panel->hasAutoRegister())

			<div class="pt-4" dusk="register-call">
				<div class="bg-gray-100 rounded-lg text-center p-4">
					<span class="text-base text-gray-700 ">Não tem uma conta?</span>
					<div class="clear-both mb-5"></div>
					
					<x-element::link full dusk="register-button" href="{{ route($panel->getRouteName('signup')) }}">Cadastre-se</x-element::link>
				</div>
			</div>
		@endif
	</form>
@endsection
