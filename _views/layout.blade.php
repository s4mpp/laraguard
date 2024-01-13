<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Laraguard - Login Starter App</title>
</head>
<body>
	<h1>@yield('title')</h1>

	@if(request()->session()->has('message') )
		<p style="color: green">{{ request()->session()->get('message') }}<p>
	@endif

	@if($errors->any())
		<ul>
			@foreach ($errors->all() as $error)
				<li style="color: red">{!! nl2br($error) !!}</li>
			@endforeach
		</ul>
	@endif

	@yield('content')
</body>
</html>
