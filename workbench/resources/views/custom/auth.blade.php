@extends($panel->layout()->getHtmlFile())

@section('body')

	<div class="bg-gray-500 min-h-full w-100 flex p-2 flex-col justify-center">
		<div class="bg-white p-10 rounded-lg sm:w-full sm:max-w-[420px]  sm:mx-auto shadow">
			<div class="text-center border-b pb-7">
				<h2 class="text-center text-gray-900  leading-9 font-bold">{{ env('APP_NAME') }}</h2>
				<span class="text-center text-gray-600  mb-4">{{ $panel->getTitle() }}</span>
			</div>

			<div class="">

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