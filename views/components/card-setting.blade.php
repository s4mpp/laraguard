<div class="grid grid-cols-1 gap-x-8 py-7 gap-y-10  md:grid-cols-3">
    <div>
        <h2 class="text-base font-semibold leading-7 text-gray-900">{{ $title }}</h2>
        <p class="mt-0 lg:mt-1 text-sm leading-6 text-gray-500">{{ $subtitle }}</p>
    </div>
    
    <div class="grid grid-cols-12 col-span-2">
        <div class="col-span-full">
            <x-alert />
        </div>
        <div class="col-span-12 sm:col-span-9 md:col-span-11 lg:col-span-8 xl:col-span-6">

            {{ $slot }}
        </div>
    </div>
</div>