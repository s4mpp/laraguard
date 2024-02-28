<x-element::modal idModal="modalPassword" title="Por favor, informe sua senha para continuar:">
	<div class="space-y-4">

		<x-element::form.input  required autocomplete="password" type="password" name="current_password"  title="Senha"></x-element::form.input>
	</div>

	<div class="flex justify-center items-center gap-4 mt-10">
		<x-element::button type="submit" className=" btn-primary">Continuar</x-element::button>
	</div>
</x-element::modal>

