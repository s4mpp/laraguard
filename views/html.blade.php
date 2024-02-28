<!DOCTYPE html>
<html class="h-full bg-gray-50">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>{{ $panel->getTitle() }} | @yield('title') </title>

		<link rel="stylesheet" href="{{ asset('vendor/laraguard/style.css') }}">
		<link rel="stylesheet" href="{{ asset('vendor/element/style.css') }}">

		<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
	</head>
	<body class="h-full">
		@yield('body')
	</body>
</html>
