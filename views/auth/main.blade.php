@extends($panel->layout()->getHtmlFile())

@section('body')
	<div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">

		<h2 class="mb-0 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">{{ env('APP_NAME') }}</h2>
		<p class="mt-0 text-sm text-center leading-6 text-gray-500">{{ $panel->getTitle() }}</p>
		
		<div class="mt-7 sm:mx-auto sm:w-full sm:max-w-[420px]">
			<div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
				<div class="sm:mx-auto sm:w-full sm:max-w-md">
					<h2 class="mb-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-700">@yield('title')</h2>
				</div>
 		
				@yield('content')
		  </div>
		</div>
	</div>
@endsection