@extends($panel->layout()->getAuthFile())

@section('title', 'Account created')

@section('content')

	<div class="text-center mb-10 text-base text-gray-700">

		<p class="text-lg font-semibold"><strong>Pronto!</strong></p>
		<p>Sua conta foi criada com sucesso!</p>
	</div>

	<a dusk="click-to-access" href="{{ route($panel->getRouteName('login')) }}" class="ease-linear transition  flex w-full justify-center rounded-md bg-gray-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">Clique aqui para acessar</a>
@endsection
