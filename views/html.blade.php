<!DOCTYPE html>
<html class="h-full bg-gray-50">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>{{ $panel_title }} | {{ $page_title }}</title>

		<script src="https://cdn.tailwindcss.com"></script>
		<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
	</head>
	<body class="h-full">
		@yield('body')
	</body>
</html>
