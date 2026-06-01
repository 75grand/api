<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap">
    @vite('resources/css/app.css')
    <script defer src="https://assets.onedollarstats.com/stonks.js"></script>

    <title>{{ $title }}</title>

    <meta name="title" content="{{ $title }}">
    <meta name="apple-itunes-app" content="app-id=6462052792">
    <meta name="theme-color" content="#0369a1">    

    <meta property="og:title" content="{{ $title }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ empty($image) ? url('/assets/placeholder.png') : $image }}">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $title }}">
    <meta property="twitter:image" content="{{ empty($image) ? url('/assets/placeholder.png') : $image }}">
</head>

<body class="antialiased">
    {{ $slot }}
</body>

</html>