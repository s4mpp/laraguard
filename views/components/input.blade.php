<div>
	<label for="name" class="block text-sm font-medium leading-6 text-gray-900">{{ $title }}</label>
	<div class="mt-2">
		<input {{ $attributes }} value="{{ old($name) ?? $slot }}" class="
			block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm 
			px-3
			ring-none
			placeholder:text-gray-400 
			outline-none
			ring-1 ring-inset ring-gray-300
			focus:ring-gray-600
			focus:ring-2 focus:ring-inset
			transition ease-linear duration-200
			sm:text-sm sm:leading-6">
	</div>
</div>