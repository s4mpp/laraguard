@extends('laraguard::html')

@section('body')
	<div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">

		<h2 class="mb-0 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">{{ env('APP_NAME') }}</h2>
		<p class="mt-0 text-sm text-center leading-6 text-gray-500">{{ $panel->getTitle() }}</p>
		
		<div class="mt-7 sm:mx-auto sm:w-full sm:max-w-[420px]">
			<div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
				<div class="sm:mx-auto sm:w-full sm:max-w-md">
					<h2 class="mb-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-700">@yield('title')</h2>
				</div>

				@session('message')
					<div class="rounded-md bg-green-50 p-4">
						<p class="text-sm font-medium text-green-800">{{ $value }}</p>
					</div>
				@endsession

				@if(isset($errors) && $errors->any())
					<div class="rounded-md bg-red-50 p-4">
						@foreach ($errors->all() as $error)
							<p class="text-sm font-medium text-red-800">{!! nl2br($error) !!}</p>
						@endforeach
					</div>
				@endif
 		
				@yield('content')
		  </div>
		</div>
	</div>
@endsection