@extends('laraguard::layout')

@section('content')

	<form action="{{ $url_save_personal_data }}" method="POST">
		@csrf
		@method('PUT')
		<div class="space-y-4" x-data="{modalPassword: false}">
			<x-element::message.flash key="message-personal-data-saved" />
			<x-element::message.error key="error-personal-data" />
			
			<x-element::form.input type="text" required name="name" title="Nome">{{ auth()->guard($guard)->user()->name }}</x-element::form.input>

			<x-element::form.input type="email" required name="email" title="E-mail" type="email">{{ auth()->guard($guard)->user()->email }}</x-element::form.input>
			
			<x-element::button className="w-full" type="button" x-on:click="modalPassword = true">Salvar</x-element::button>

			<x-laraguard::modal-password />
		</div>
	</form>
@endsection
