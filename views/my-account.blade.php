@extends('laraguard::layout')

@section('content')

	
	

	<div class="divide-y divide-gray-300">
		<x-laraguard::card-setting title="Personal Information" subtitle="Use a permanent address where you can receive mail">
			
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
		</x-laraguard::card-setting>

		<x-laraguard::card-setting title="Change password" subtitle="Update your password associated with your account">
			
			<form action="{{ $url_save_password }}" method="POST">
				@csrf
				@method('PUT')
				<div class="space-y-4">
					<x-element::message.flash key="message-password-changed" />
					<x-element::message.error key="error-password" />
					
					<x-element::form.input type="password" required autocomplete="new-password" type="password" name="current_password"  title="Senha atual"></x-element::form.input>
						
					<x-element::form.input type="password" required autocomplete="new-password" type="password" name="password" title="Digite a nova senha"></x-element::form.input>
					
					<x-element::form.input type="password" required autocomplete="new-password" type="password" name="password_confirmation" title="Repita a nova senha"></x-element::form.input>

					<x-element::button className="w-full" type="submit">Alterar</x-element::button>
				</div>
			</form>
		</x-laraguard::card-setting>
	</div>
@endsection
