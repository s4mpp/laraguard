<x-laraguard::modal idModal="modalPassword" title="Por favor, informe sua senha para continuar:">
	<div class="space-y-4">

		<x-laraguard::input required autocomplete="password" type="password" name="current_password"  title="Senha"></x-laraguard::input>
	</div>

	<div class="flex justify-center items-center gap-4 mt-10">
		<x-laraguard::button type="submit" className=" btn-primary">Continuar</x-laraguard::button>
	</div>
</x-laraguard::modal>

