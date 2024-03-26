@extends($panel->layout()->getAuthFile())

@section('title', 'Recuperar senha')

@section('content')

	<x-element::message.flash />
	<x-element::message.error />

	<form class="space-y-6"  method="POST" action="{{ route($panel->getRouteName('send_link_password')) }}">
		@csrf

		<x-element::form.input required type="text" name="email" title="E-mail" />

		<x-element::button full type="submit">Enviar e-mail</x-element::button>
	
		<p class="mt-10 text-center text-sm text-gray-500">
			<a tabindex="-1" href="{{route($panel->getRouteName('login')) }}" class="font-semibold leading-6 text-gray-600 hover:text-gray-500">Voltar</a>
		</p>
	</form>
@endsection