@extends($panel->layout()->getAuthFile())

@section('title', 'Account created')

@section('content')

	<div class="text-center mt-4 mb-10 text-base">

		<p class="text-lg text-gray-800 font-semibold"><strong>Pronto!</strong></p>
		<p class="text-gray-500 text-base">Sua conta foi criada com sucesso!</p>
	</div>

	<x-element::link full dusk="click-to-access" href="{{ route($panel->getRouteName('login')) }}" >Clique aqui para acessar</x-element::link>
@endsection
