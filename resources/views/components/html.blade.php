<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="icon" href="{{ url('/assets/icon.svg') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap">
    <title>{{ $title }}</title>
    @vite('resources/css/app.css')
</head>

<body class="antialiased">
    {{ $slot }}
</body>

</html>