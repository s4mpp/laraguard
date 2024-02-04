@if(isset($errors) && $errors->any())
	<div class="rounded-md bg-red-50 p-4">
		@foreach ($errors->all() as $error)
			<p class="text-sm font-medium text-red-800">{!! nl2br($error) !!}</p>
		@endforeach
	</div>
@endif