<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>{{ $title }}</title>
    @vite('resources/css/app.css')
</head>

<body class="antialiased">
    {{ $slot }}
</body>

</html>