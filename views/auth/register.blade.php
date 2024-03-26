@extends($panel->layout()->getAuthFile())

@section('title', 'Register')

@section('content')

	<form class="space-y-6" method="POST" action="{{ route($panel->getRouteName('create_account')) }}">
		@csrf

		<x-element::form.input required type="text" name="name" title="Nome" />

		<x-element::form.input required type="email" name="email" title="E-mail" />

		<x-element::form.input required  autocomplete="new-password" type="password" name="password" title="Senha" />

		<x-element::button full dusk="register" type="submit">Cadastrar</x-element::button>

		<p class="mt-10 text-center text-sm text-gray-500">
			<a tabindex="-1" href="{{route($panel->getRouteName('login')) }}" class="font-semibold leading-6 text-gray-600 hover:text-gray-500">Voltar</a>
		</p>
		
	</form>
@endsection
