@extends($panel->layout()->getHtmlFile())

@section('title', $page_title)

@section('body')
	<div class="min-h-full"  x-data="{ menuDropdownOpen: false }">
		<nav class="bg-gray-800">
			<div class="mx-auto max-w-7xl px-2 sm:px-4 lg:px-8">
			  <div class="relative flex h-16 items-center justify-between lg:border-b lg:border-gray-400 lg:border-opacity-25">
				<div class="flex items-center px-2 lg:px-0">
				  <div class="flex-shrink-0">
					<a href="{{ $home_url }}"><span class="text-white font-semibold">{{ env('APP_NAME') }}</span></a>
	
				   </div>
				  <div class="hidden sm:ml-10 sm:block">
					<div class="flex items-center">
						@foreach($menu_links as $n => $menu_item)
							@if(!$menu_item->hasSubmenu())
								<a @class([
									'rounded-md px-3 py-2 text-sm font-medium transition-colors ease-in',
									'text-white hover:bg-gray-700' => !$menu_item->isActive(),
									'bg-gray-900 text-white' => $menu_item->isActive()
								]) href="{{ $menu_item->getAction() }}">{{ $menu_item->getTitle() }}</a>
							@else
								<x-element::dropdown>
									<x-slot:button>
										<a @class([
											'rounded-md px-3 py-2 text-sm font-medium transition-colors ease-in',
											'text-white hover:bg-gray-700' => !$menu_item->isActive(),
											'bg-gray-900 text-white' => $menu_item->isActive()
										]) x-on:click="toggleDropdown()" href="#">
											<span class="inline-flex">{{ $menu_item->getTitle() }} <x-element::icon class=" w-5 h-5" name="chevron-down" solid mini /></span>
										</a>
									</x-slot:button>
									<x-slot:body>
										@foreach($menu_item->getSubMenuItems() as $sub_menu_item)
											<x-element::dropdown.link class="whitespace-nowrap" href="{{ $sub_menu_item->getAction() }}">{{ $sub_menu_item->getTitle() }}</x-element::dropdown.link>
										@endforeach
									</x-slot:body>
								</x-element::dropdown>
							@endif
						@endforeach
					</div>
				  </div>
				</div>
				 
				{{-- <div class="flex sm:hidden">
				  <button type="button" x-on:click="menuDropdownOpen = !menuDropdownOpen" class="relative inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none  focus:ring-offset-gray-800" aria-controls="mobile-menu" aria-expanded="false">
						<span class="absolute -inset-0.5"></span>
						<span class="sr-only">Open main menu</span>
						<!-- Menu open: "hidden", Menu closed: "block" -->
						<svg x-cloak :class="(!menuDropdownOpen) ? 'block' : 'hidden'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
						</svg>
						<svg x-cloak :class="(menuDropdownOpen) ? 'block' : 'hidden'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</div> --}}
				
				
				<div class="sm:ml-4 sm:block">
				  <div class="flex items-center">
					<div class="relative" x-data="{ dropdownCustomerMenu: false }">
						<button  x-on:click="dropdownCustomerMenu = !dropdownCustomerMenu" type="button" class="-m-1.5 flex items-center p-1.5" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
						  <span class="sr-only">Open user menu</span>
						  
						  <span class="inline-block h-7 w-7 overflow-hidden rounded-full bg-slate-100">
							  <svg class="h-full w-full text-slate-300" fill="currentColor" viewBox="0 0 24 24">
								<path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
							  </svg>
							</span>
						</button>
			
						<div x-cloak
							x-on:click.outside="dropdownCustomerMenu = false;"
							x-show="dropdownCustomerMenu"
							x-transition:enter="transition ease-out duration-100"
							x-transition:enter-start="transform opacity-0 scale-95"
							x-transition:enter-end="transform opacity-100 scale-100"
							x-transition:leave="transition ease-in duration-75"
							x-transition:leave-start="transform opacity-100 scale-100"
							x-transition:leave-end="transform opacity-0 scale-95"
						
						class="absolute divide-y divide-slate-100 right-0 z-10 mt-2.5   origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-slate-900/5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
					  
						<div class="px-4 py-3" role="none">
							<p class="text-sm" role="none">{{ auth()->guard($guard_name)->user()->name }}</p>
							<p class="truncate text-sm font-medium text-slate-900" role="none">{{ auth()->guard($guard_name)->user()->email }}</p>
						</div>
						<div class="py-1" role="none">
							 

							<a href="{{ $logout_url }}" class="text-red-700 flex justify-between items-center  font-semibold transition-colors px-4 py-2 text-sm bg-red-50 hover:bg-red-100" role="menuitem" tabindex="-1" id="user-menu-item-1">
								Sair
							  
							  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
								  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
							  </svg>
						  </a>
						</div>
						</div>
					  </div>
				  </div>
				</div>
			  </div>
			</div>
	  
			<!-- Mobile menu, show/hide based on menu state. -->
			{{-- <div class="lg:hidden" id="mobile-menu" x-show="menuDropdownOpen">
			  <div class="space-y-1 px-2 pb-3 pt-2">
	
				@foreach($menu as $n => $menu_item)
					
					<a @class([
						'block rounded-md py-3 my-3 text-center px-3 text-base font-medium transition-colors ease-in',
						'text-white hover:bg-gray-700' => !$menu_item['active'],
						'bg-gray-900 text-white' => $menu_item['active']
					]) href="{{ route($menu_item['route']) }}">{{ $menu_item['title'] }}</a>
				@endforeach
	
			  </div>
			  <div class="border-t border-gray-700 pb-3 pt-4">
				<div class="flex items-center justify-start px-5">
				  <div class="flex-shrink-0">
					<span class="inline-block mt-2 h-8 w-8 overflow-hidden rounded-full bg-slate-100">
						<svg class="h-full w-full text-slate-300" fill="currentColor" viewBox="0 0 24 24">
						  <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
						</svg>
					  </span>
				  </div>
				  <div class="ml-3">
					<div class="text-base font-medium text-white">{{ Auth::user()->name }}</div>
					<div class="text-sm font-medium text-gray-300">{{ Format::cpfCnpj(Auth::user()->document) }}</div>
				  </div>

				  <div>
						<a href="#" class="rounded-full bg-red-100 ms-auto  p-2 text-red-500 hover:bg-red-200 ">
								
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
								<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
							</svg>
						</a>
					
						<a href="#" class="rounded-full bg-red-100 ms-auto  p-2 text-red-500 hover:bg-red-200 ">
							
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
								<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
							</svg>
						</a>
					 </div>
				 </div>

				 <div class="mt-3 space-y-1 px-2">
					<a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Minha conta</a>
				  </div>
			  </div>
			</div> --}}
		  </nav>

		  <header class="bg-white shadow">
			<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

				<div>
					<div class="flex" aria-label="Breadcrumb">
						<div class="flex items-center space-x-2">
							<div class="text-gray-400 ">
								{{-- <a href="#" class="text-gray-400 hover:text-gray-500"> --}}
									<svg class="h-4 w-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
										<path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z" clip-rule="evenodd" />
									</svg>
									<span class="sr-only">Home</span>
								{{-- </a> --}}
							</div>

							@foreach($breadcrumbs as $breadcrumb)
								<div class="flex items-center">
									<svg class="h-5 w-5 mt-1 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
										<path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
									</svg>
									
									@if($route = $breadcrumb->getRoute())
										<a href="{{ route($route) }}">
									@endif
										<span class="ml-2  text-sm font-medium text-gray-500">{{ $breadcrumb->getTitle() }}</span>
									@if($route)
										</a>
									@endif
								</div>
							@endforeach
						</div>
					</div>
				</div>

				<h1 class="text-3xl mt-1 font-bold tracking-tight text-gray-900">{{ $page_title }}</h1>
			</div>
		  </header>

		  <main>
			<div class="mx-auto max-w-7xl py-6 px-4 py-6 sm:px-6 lg:px-8">
				@yield('content')
			</div>
		  </main>
	</div>
@endsection