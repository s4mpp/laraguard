@extends('laraguard::layout')

@section('content')
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
@endsection
