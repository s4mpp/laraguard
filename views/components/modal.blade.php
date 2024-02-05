
<div class="absolute z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak x-show="{{ $idModal }}">
	<div
		x-cloak
		x-show="{{ $idModal }}"
		x-transition:enter="ease-out duration-300"
		x-transition:enter-start="opacity-0"
		x-transition:enter-end="opacity-100"
		x-transition:leave="ease-in duration-200"
		x-transition:leave-start="opacity-100"
		x-transition:leave-end="opacity-0"
	class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

	<div class="fixed inset-0 z-10 w-screen overflow-y-auto">
	<div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">

		<div
			x-cloak
			x-show="{{ $idModal }}"
			x-transition:enter="ease-out duration-300" 
			x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
			x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
			x-transition:leave="ease-in duration-200" 
			x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
			x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
			class="absolute transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 w-full sm:max-w-lg">
			
			<div class="px-4 pt-5 sm:p-6 sm:pb-4">
				<div class="flex justify-start align-center items-center">
					<div class="w-full sm:text-left">
						<div class="flex  w-full justify-between items-center">
							<h3 class="text-base font-semibold  text-gray-900" id="modal-title">{{ $title }}</h3>
							<button x-on:click="{{ $idModal }} = false" type="button" class="text-gray-500 p-2 hover:text-gray-700 transition-colors">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
									<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
								</svg>
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="bg-white px-4 pb-6 sm:px-6">
				{{ $slot }}
			</div>
		</div>
	</div>
	</div>
</div>