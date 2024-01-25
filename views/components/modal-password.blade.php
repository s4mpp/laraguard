<x-modal idModal="modalPassword" title="Senha" subtitle="Por favor, informe sua senha para continuar:">
	<form wire:submit.prevent="{{ $action }}" x-data="{loading: false}" x-on:submit="loading = true" x-on:reset-form.window="loading = false" >
		<div class="space-y-4">

			<x-input required autocomplete="password" type="password" name="password" wire:model.defer="password" title="Senha"></x-input>
		</div>

		<div class="flex justify-center items-center gap-4 mt-10">
			<x-button :loading=false type="button" x-on:click="modalPassword = false" className="ring-inset ring-1 ring-gray-200 btn-muted">Voltar</x-button>
			<x-button type="submit" className=" btn-primary">Continuar</x-button>
		</div>
	</form>
</x-modal>