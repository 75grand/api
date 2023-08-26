<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap">
    @vite('resources/css/app.css')
    <script async src="https://beamanalytics.b-cdn.net/beam.min.js" data-token="dc450d08-0ea1-4a44-8ba3-ef4139b189a1"></script>

    <title>{{ $title }}</title>
    <meta name="apple-itunes-app" content="app-id=6462052792">
</head>

<body class="antialiased">
    {{ $slot }}
</body>

</html>