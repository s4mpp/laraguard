@extends('laraguard::layout')

@section('content')

	<x-laraguard::errors />
	<x-laraguard::flash-message />

	<div class="divide-y divide-gray-300">
		<x-laraguard::card-setting title="Personal Information" subtitle="Use a permanent address where you can receive mail">
			<form action="{{ $url_save_personal_data }}" method="POST">
				@csrf
				@method('PUT')
				<div class="space-y-4" x-data="{modalPassword: false}">
					<x-laraguard::input required name="name" title="Nome">{{ auth()->guard($guard)->user()->name }}</x-laraguard::input>

					<x-laraguard::input required name="email" title="E-mail" type="email">{{ auth()->guard($guard)->user()->email }}</x-laraguard::input>
					
					<x-laraguard::button type="button" x-on:click="modalPassword = true">Salvar</x-laraguard::button>

					<x-laraguard::modal-password />
				</div>
			</form>
		</x-laraguard::card-setting>

		<x-laraguard::card-setting title="Change password" subtitle="Update your password associated with your account">
			<form action="{{ $url_save_password }}" method="POST">
				@csrf
				@method('PUT')
				<div class="space-y-4">
					<x-laraguard::input required autocomplete="new-password" type="password" name="current_password"  title="Senha atual"></x-laraguard::input>
						
					<x-laraguard::input required autocomplete="new-password" type="password" name="password" title="Digite a nova senha"></x-laraguard::input>
					
					<x-laraguard::input required autocomplete="new-password" type="password" name="password_confirmation" title="Repita a nova senha"></x-laraguard::input>

					<x-laraguard::button type="submit">Alterar</x-laraguard::button>
				</div>
			</form>
		</x-laraguard::card-setting>
	</div>
@endsection
